<?php

return [
    [
        'name' => 'wechat',
        'title' => '微信',
        'type' => 'array',
        'content' => [],
        'value' => [
            'appid' => 'wx06e2528db326a680',
            'app_id' => '',
            'app_secret' => '',
            'miniapp_id' => '',
            'mch_id' => '1520617461',
            'key' => 'rvbl1z1vyeit5mjry42gempz0m0nuczs',
            'mode' => 'normal',
            'sub_mch_id' => '',
            'sub_appid' => '',
            'sub_app_id' => '',
            'sub_miniapp_id' => '',
            'notify_url' => '/addons/epay/api/notifyx/type/wechat',
            'cert_client' => '/addons/epay/certs/apiclient_cert.pem',
            'cert_key' => '/addons/epay/certs/apiclient_key.pem',
            'log' => '1',
        ],
        'rule' => '',
        'msg' => '',
        'tip' => '微信参数配置',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'alipay',
        'title' => '支付宝',
        'type' => 'array',
        'content' => [],
        'value' => [
            'app_id' => '2018120962503035',
            'mode' => 'normal',
            'notify_url' => '/addons/epay/api/notifyx/type/alipay',
            'return_url' => '/addons/epay/api/returnx/type/alipay',
            'private_key' => 'MIIEpQIBAAKCAQEA6hS4vitWEQKXJ6ntSZoAvUlYlcgeAdMBODkkg4ZDWfVAU5W47QMH9k21ZNnq/0sYXab9DvGia1N8qCR7lRxl96WGUq/5K+ScVaGXzlN/+aret/PciQoAJB6MWcR/LsSeWodKkG6MTQIuPLZbNPLPmER4S8G5VBTp/snF9PeMtnuDhSxw8yPL7i++thokldebf9PZf041ATzDF20A8lj7M37c1z96LBMF0jFT6hu0pwxLkauX6fDylD9LFJlvfZGd7ZwgD9veD1eWX6haZDHCECpwoZ2MIDygHFqDdQb4hikrIN/vCe2ksH1IlUDxQJOXDAV4TU15kdC09kKtJldU7QIDAQABAoIBAQDQ3jiSh7y+ZN9XZwryd9ZdEEtZKz2LRyp5bpOkQHNsm6gQbTKbWe8K2gAXw1MrBWjyeASqBvZ0agR7TEJxpOtfdHVM5vShM9ZDmnMIif1RXCRrY8/O//tsP03g87LAmnTqNnpUMjBCrsVvKxBrSJXwOnhUsMGbwyWgUY6vrQwD7rEXEBkN20WGhWW3t3e0H12x1NhA1Ay+TuGG2tACd3lUGcDh4WiVTKJo0hr7KxULvclSJcz9YRTvSmNLhU6wBtsGHm969r9E+Ya2gTc3Lsjm8woG5LHEuuM4GQ3OsDLTbZ2/LC2VdPh1G76LurGdLoSHS2SCZSoLnB2ZSypdtNcJAoGBAP5fIs2ctFrkcwnyz0GcC+rOXsIL784PO+EXVOlgftAMaYc02t6wvY5Sd+9iMoxIM0z6XZ7U8DLugTKKJnwAClD0j5hIPkaDoNEfAwru3XAUcJx9IOIcRfZlwmGkTFp5Om8GTmFlOILzMTW4keLGsGLFTRCvfrT1vPIfXJPvQWmDAoGBAOuUVVZDh+pqW6awIWWtuUu8474KqUfKvKuEOIbzYZb1y1aL6GRc/a+mXk+S1ldiMEXRFeO0cp7QHPDckoN3sM45mKmstYu6JjYDeMyLnuYMKzmH26GhW2T2GKCPYOBZJ8evCyC8d6SN0Mo88b/hEdY+Zyw4dM3Vonph0WA6BazPAoGBAILTFyk8kR22JNxW/vU1doObA+01Cm6mMxu/TjKBKHeECro5GuAxcixCieCaVnkDoS7UDVZlGex38ga2OrjHpIaiYT/m1/CYPhuPoZb75vN/77LsP/9Kn0jsaR6iy4o0kzMKwUEiOABRZ1vDw3Qo0Arcgpg0SixzvmugaZLEGJZVAoGBAKS+g/simL8HB/cGvyRDHAzqGH4DKKSrE8wCWCGPvFR8qWlpx+e/3Czk9JlDP5+2a7m3YU5vF6UWNtb9+Xv0zPE6+BbYT9FaKXSmcujgTwvjwKqn5qEenTJ6o3hNLFtYHeiX8fk/s4+hGT0E567e78FavH4BMTXsy4Y4N9rKWMOjAoGAdeVcEwm+fh07MK5F4bGdt9RFycs0TiGwNxBWhz2ZoJE/Glmk7UuOmB7CoPzoN0WnEqB0zEVPxR2j0Tdvlmuqy/zs01vmJgBKhvoz4bE7u4QsDbUsQtxJMx3vm0h2lqEvqkjMh19SmSw15ZIwaIwrwwLsuipcltjq+0XfaJFs6Ms=',
            'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjRwtPP0lKCAIXrvBlasv99BqQej95eO9DfwboVhVgAyoPChsYCKiBRz1dYGh6XccQ67WvTEEa92/ivzGYBpLcsMTCci3l+YlhSUHzn5uOuUSoUEPIbixaj+fer6zPhpx1puYIKKWKOrTtO1UUitqduFSo9cqeglkSOSQMQ6WnPVeSxEz5s8xDxWYYmN6q18b3fDnTG4W4uSgjMhs8TSwgcDVkrQLRPoDK41m3GyfdXu4Ws+I5oTKwChlKdvCK14PlLsGyQSyQqi4f7KaLoKZ7ASXNfih9YJ6Yw4ifSrz9ptHtLxfvaLEZjqckgK3FoTP2mvHlDnFIYVQaamn97ojnQIDAQAB',
            'app_cert_public_key' => '',
            'alipay_root_cert' => '',
            'log' => '1',
            'scanpay' => '0',
        ],
        'rule' => 'required',
        'msg' => '',
        'tip' => '支付宝参数配置',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => '__tips__',
        'title' => '温馨提示',
        'type' => 'array',
        'content' => [],
        'value' => '请注意微信支付证书路径位于/addons/epay/certs目录下，请替换成你自己的证书<br>appid：APP的appid<br>app_id：公众号的appid<br>app_secret：公众号的secret<br>miniapp_id：小程序ID<br>mch_id：微信商户ID<br>key：微信商户支付的密钥',
        'rule' => '',
        'msg' => '',
        'tip' => '微信参数配置',
        'ok' => '',
        'extend' => '',
    ],
];
