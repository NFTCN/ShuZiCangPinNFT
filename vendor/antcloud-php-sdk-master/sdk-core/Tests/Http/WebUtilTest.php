<?php

namespace Tests\Util;

require_once '../../AntCloudSDKCore/Config.php';

use AntCloudSDKCore\Http\HttpRequest;
use AntCloudSDKCore\Http\WebUtil;
use PHPUnit\Framework\TestCase;

class WebUtilTest extends TestCase
{

    public function testBuildCustomFormParams()
    {
        $param = array(
            'qinghua' => array(
                'teacher' => array(
                    'name' => 'aaa',
                    'sex' => 'male',
                    'age' => 19,
                ),
                'students' => array(
                    array(
                        'name' => 's1',
                        'sex' => 'female',
                        'age' => 10,
                    ),
                    array(
                        'name' => 's2',
                        'sex' => 'male',
                        'age' => 20,
                    ),
                    array(
                        'name' => 's3',
                        'sex' => 'female',
                        'age' => 30,
                    ),
                ),
            ),
        );

        $result = WebUtil::buildCustomFormParams($param);
        $expected = array(
            'qinghua.teacher.name' => 'aaa',
            'qinghua.teacher.sex' => 'male',
            'qinghua.teacher.age' => 19,
            'qinghua.students.1.name' => 's1',
            'qinghua.students.1.sex' => 'female',
            'qinghua.students.1.age' => 10,
            'qinghua.students.2.name' => 's2',
            'qinghua.students.2.sex' => 'male',
            'qinghua.students.2.age' => 20,
            'qinghua.students.3.name' => 's3',
            'qinghua.students.3.sex' => 'female',
            'qinghua.students.3.age' => 30,
        );

        $this->assertEquals($expected, $result);
    }

    public function testBuildCustomFormParamsWithEmpty()
    {
        $this->assertEquals(array(), WebUtil::buildCustomFormParams(array()));
    }
}
