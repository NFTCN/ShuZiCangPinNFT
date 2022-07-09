<?php

namespace app\admin\controller\nft;

use app\common\controller\Backend;
use app\common\service\nft\PayService;
use fast\Random;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\Model\User;
use think\File;
use think\helper\Str;

/**
 * 收藏品
 *
 * @icon fa fa-circle-o
 */
class Collection extends Backend
{

    /**
     * Collection模型对象
     * @var \app\admin\model\nft\Collection
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\nft\Collection;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("stateList", $this->model->getStateList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['issuer','author'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','type','title','image','price','stock','market','startdate','times','state', 'tag']);
                $row->visible(['issuer']);
				$row->getRelation('issuer')->visible(['name']);
				$row->visible(['author']);
				$row->getRelation('author')->visible(['name','avatar']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    $image = getimagesize(cdnurl($params['image'],true));
                    if(empty($image)){
                        $this->error('图片资源无效,请重新上传');
                    }
                    if($image[0] != $image[1]){
                        $this->error('非正方形缩略图资源');
                    }
                    if($image[0] < 800){
                        $this->error('尺寸过小,请最少上次800 * 800 的资源');
                    }

                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();

    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                $result = false;
                Db::startTrans();
                try {
                    $image = getimagesize(cdnurl($params['image'],true));
                    if(empty($image)){
                        exception('图片资源有问题');
                    }
                    if($image[0] != $image[1]){
                        exception('图片非比正方形');
                    }
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    public function airdrop($ids = null)
    {
        if($this->request->isPost()){
            $post = $this->request->post('row/a');
            $user_ids = $post['user_id'];
            if(empty($user_ids)){
                $this->error('未选择空投对象');
            }
            $collection = \addons\nft\model\Collection::where('id',$ids)->find();
            if(empty($collection)){
                $this->error('未找到该藏品');
            }
            $user_ids = explode(',', $user_ids);
            $data = [];
            $key = 'collection_'.$ids;
            if(!PayService::checkoutStock($key, count($user_ids))){
                $this->error('请检查空投藏品是否充足');
            }

            foreach ($user_ids as $user_id) {
                $data[] = [
                    'user_id'=>$user_id,
                    'collection_id'=>$ids,
                    'limit_time'=>time() + 86400
                ];
            }
            model(\app\admin\model\nft\AirDrop::class)->saveAll($data);
            $this->success('空投成功');
        }
        return $this->view->fetch();
    }

    public function otherairdrop($ids = null)
    {
        if($this->request->isPost()){
            $post = $this->request->post('row/a');
            if (!in_array($post['type'], ['random', 'all']) || (int)($post['num']) <= 0) {
                $this->error('请设置空投参数');
            }
            $collection = \addons\nft\model\Collection::where('id',$ids)->find();
            if(empty($collection)){
                $this->error('未找到该藏品');
            }
            $total = count(User::all());
            $num = ($post['type'] == 'all') ? $total : $post['num'];
            if ($num > $total) {
                $num = $total;
            }
            $key = 'collection_'.$ids;
            if(!PayService::checkoutStock($key, $num)){
                $this->error('请检查空投藏品是否充足');
            }
            if ($post['type'] == 'random') {
                $author = collection((new User)->orderRaw('rand()')->field('id')->limit($num)->select())->toArray();
            } else {
                $author = collection((new User)->field('id')->select())->toArray();
            }

            $data = [];
            foreach ($author as $val) {
                $data[] = [
                    'user_id'=>$val['id'],
                    'collection_id'=>$ids,
                    'limit_time'=>time() + 86400
                ];
            }
            model(\app\admin\model\nft\AirDrop::class)->saveAll($data);
            $this->success('空投成功');
        }
        return $this->view->fetch();
    }

}
