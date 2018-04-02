<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */
Route::group(['namespace' => 'Wap', 'middleware' => 'user_visit_log'], function () {
    //首页
    Route::get('/', "IndexController@getIndex");

    //优惠券
    Route::get('/coupon-list', "CouponController@getCouponList");

    //领取优惠券
    Route::get('/get_coupon', "CouponController@getCouponCard");

    // 领取红包
    Route::get('/get_redcard', "CouponController@getRedCard");
    //下载商品索引
    Route::get('/xjsp', "GoodsController@getOffSaleGoodsList");

    //产品库
    Route::get('/cpk', "GoodsController@getGoodsLibrary");

    // 店铺首页
    //明星商家列表
    Route::get('/shop_star', 'ShopController@shopStar');
    // /^\d*[1-9]+\d*?$/匹配正整数，包括01,002,00232这样的数字也认为是合理的
    Route::get('/shop-{shop_id}.html', "ShopController@index")->where('shop_id', '^\d*[1-9]+\d*?$');

    // 店铺推荐商品
    Route::any('/shop_mobileshelf-{shop_id}.html', "ShopController@mobileShelf")->where('shop_id', '^\d*[1-9]+\d*?$');

    // 药店资质
    Route::get('/shop_info-{shop_id}.html', "ShopController@info")->where('shop_id', '^\d*[1-9]+\d*?$');

    // 店铺搜索页 - 分类
    Route::any('/shop_search-{shop_id}-{cid}.html', "ShopController@search")
        ->where('shop_id', '^\d*[1-9]+\d*?$')
        ->where('cid', '^\d*[1-9]+\d*?$');

    // 店铺搜索页 - 关键字
    Route::any('/shop_search-{shop_id}.html', "ShopController@keywordSearch")
        ->where('shop_id', '^\d*[1-9]+\d*?$');

    // 店铺分类页
    Route::get('/shop_cates-{shop_id}.html', "ShopController@categories")->where('shop_id', '^\d*[1-9]+\d*?$');

    // 商品详情页
    Route::get('/product-{shop_id}-{goods_id}.html', "GoodsController@getGoodsInfo")
        ->where('shop_id', '^\d*[1-9]+\d*?$')
        ->where('goods_id', '^\d*[1-9]+\d*?$');

    //收藏商品
    Route::post('/collection', 'CollectionController@store')->middleware('auth2');

    // 评论列表页--测试
    Route::get('/comment-{shop_id}-{goods_id}.html', "GoodsController@getComment")
        ->where('shop_id', '^\d*[1-9]+\d*?$')
        ->where('goods_id', '^\d*[1-9]+\d*?$');

    //药目分类
    Route::get('cpk/drug_group-{base_id}.html', 'GoodsController@getDrugGroup');

    // 错误页面路由（找不到商品或者药目使用）
    Route::get('/non-page', 'ErrorController@getIndex');

    // 获取购物车数量接口
    Route::post('/cart_num', 'GoodsController@postCartNum');

    // 获取商品价格接口
    Route::post('/goods_price', 'GoodsController@postProductPrice');
    //购物车
    Route::controller('/cart', 'CartController');

    // 订单确认
    Route::controller('/order', 'OrderController');

    // 订单地址操作
    // 这些路由暂时放在MemberController里.如果需要做wap版用户中心,可以将这些路由归置汇总
    // huangcan
    Route::post('/add-address', 'MemberController@addAddress'); // 保存新地址
    Route::get('/new-address', 'MemberController@newAddress'); // 新建地址
    Route::get('/edit-address', 'MemberController@getEditAddress'); // 修改地址
    Route::post('/edit-address', 'MemberController@postEditAddress'); // 修改地址
    Route::get('/select-province', 'MemberController@selectProvince'); //选择省份
    Route::get('/select-city', 'MemberController@selectCity');

    // 获取商品价格接口
    Route::post('/goods_price', 'GoodsController@postProductPrice');

    Route::get('/list-addresses', 'MemberController@listAddress'); // 地址列表

    Route::get('/pay/list/{pid}/{orderno?}/{type?}', 'PayController@getIndex'); //支付订单
    Route::get('/pay/offline', 'PayController@getOffline'); //货到付款订单
    Route::post('/pay/notify', 'PayController@postNotify'); //异步回调支付订单
    Route::get('/pay/return', 'PayController@getReturn'); //同步支付成功
    Route::get('/pay/prompt/{pid}/{orderno?}', 'PayController@getPrompt'); //支付提醒跳转

    // 按病找药页
    Route::get('/category/1_{illness}.html', "CategoryController@getDrugCategory");

    // 按病搜索
    Route::get('/category/2_{categoryId}.html', 'SearchController@getCategorySearchList');

    //分类找药页
    Route::get('/category/allSorts.html', 'CategoryController@getCatalog');

    //问答详情页
    Route::get('/ask/question-{question_id}.html', 'ConsultativeController@getConsultative')->where('question_id', '^\d*[1-9]+\d*?$');

    //登陆
    Route::get('/login', 'LoginController@getIndex');
    //提交登录
    Route::post('/login', 'LoginController@postIndex');
    Route::post('/check_mobile', 'LoginController@checkMobile'); //检测手机是否存在 post
    Route::post('/send/sms', 'LoginController@postSendSms')->middleware(['captcha']); //发送短信 post
    Route::get('/login/phone', 'LoginController@getPhone'); //手机登录
    Route::post('login/vcode', 'LoginController@postLoginByVcode')->middleware(['captcha','sms_code']); //手机验证码快捷登录 post
    Route::get('/forget/password', 'LoginController@getForgetPassword'); //忘记密码
    Route::post('/forget/password/update', 'LoginController@postForgetPassword'); //忘记密码提交

    Route::get('/register', 'RegisterController@getIndex'); //注册
    Route::post('/register', 'RegisterController@store'); //注册提交

    //点赞活动
    Route::get('advise', 'AdviceController@getIndex'); //点赞活动页面
    Route::post('advise', 'AdviceController@postAdvice')->middleware('auth2'); //提交留言

    /**
     * 个人中心路由存放模块
     */
    //图形验证码
    Route::get('/captcha', 'LoginController@getCaptcha');
    Route::group(['prefix' => '/personal', 'middleware' => 'auth2'], function () {
        Route::get('logout', 'PersonalCenterController@getLogout');

        //个人中心主页
        Route::get('/', 'PersonalCenterController@getIndex');

        //修改绑定手机
        Route::get('/modify/bind/phone', 'PersonalCenterController@getModifyPhone');

        //修改绑定手机 密码校验
        Route::post('/modify/check/password', 'PersonalCenterController@postCheckPassword');

        //修改绑定手机第二步
        Route::get('/modify/bind/phone/next', 'PersonalCenterController@getModifyPhoneSecond');

        //修改绑定手机第二步提交
        Route::post('/modify/phone/next/submit', 'PersonalCenterController@postModifyPhoneSecond');

        //修改绑定手机完成
        Route::get('/modify/bind/phone/finished', 'PersonalCenterController@getModifyPhoneFinished');

        //检测手机号码是否可绑定
        Route::post('/mobile/isbind', 'BindController@postCheckMobileIsBind');

        //第三方登陆绑定手机
        Route::get('/authorization/bind/phone', 'BindController@getIndex');

        //第三方登陆绑定手机提交
        Route::post('/authorization/bind/phone', 'BindController@postAuthorizationBindMobile');

        //个人中心绑定手机
        Route::get('/bind/phone', 'PersonalCenterController@getCenterBindMobile');

        //个人中心绑定手机
        Route::post('/bind/phone/submit', 'PersonalCenterController@postCenterBindMobile');

        //个人中心账号管理
        Route::get('/account', 'PersonalCenterController@getAccountNumber');

        //个人中心修改密码
        Route::get('/account/password/edit', 'PersonalCenterController@getModifyPassword');

        //个人中心修改密码提交
        Route::post('/account/password/submit', 'PersonalCenterController@postModifyPassword');

        //订单管理列表
        Route::get('/orders', 'OrderController@getOrderManage');

        //订单详情
        Route::get('/orders/info/{oid}', 'OrderController@getOrderInfo');

        //订单管理--待付款
        Route::get('/orders/payment', 'OrderController@getPayment');

        //订单管理--待收货
        Route::get('/orders/harvested', 'OrderController@getHarvested');

        //订单管理--待评价
        Route::get('/orders/evaluated', 'OrderController@getEvaluated');

        //订单管理--删除订单
        Route::post('/orders/del', 'OrderController@postDelOrderByOid');

        //订单管理--取消订单
        Route::post('/orders/cencel', 'OrderController@postCancelOrderByOid');

        //订单管理--确认收货
        Route::post('/orders/shouhuo', 'OrderController@postMakeSureGetProduct');

        //订单评价
        Route::get('/orders/evaluate/{order_id}', 'OrderController@getOrderEvaluate');

        //订单评价提交
        Route::post('/orders/evaluate/submit', 'OrderController@postOrderEvaluate');

        //订单管理--退款
        Route::get('/orders/refund', 'OrderController@getRefund');

        // 订单管理--退款申请

        Route::get('/orders/refund/apply/{oid}', 'OrderController@getRdfundApply');

        // 退款申请提交
        Route::post('/orders/refund/apply', 'OrderController@postRdfundApply');

        // 订单管理--退款完成
        Route::get('/orders/refund/info/{order_id}', 'OrderController@getRdfundInfo');

        // 每月订单总数
        Route::get('/TotalOrders', 'OrderController@getTotalOrders');

        // 抽奖概率逻辑以及数据处理
        Route::get('/Lottery', 'OrderController@Lottery');

        //个人中心收藏
        Route::get('/collection', 'CollectionController@getIndex');
        //删除收藏
        Route::post('/collection/delete/{id}', 'CollectionController@postDelete');

        //个人中心地址列表
        Route::get('/address', 'PersonalCenterController@getAddressList');

        //个人中心修改地址
        Route::get('/address/{id}/eidt', 'PersonalCenterController@getEditAddress');

        //个人中心修改地址提交
        Route::post('/address/{id}/submit', 'PersonalCenterController@postEditAddress');

        //个人中心新增地址
        Route::get('/address/create', 'PersonalCenterController@getAddressAdd');

        //个人中心新增地址保存
        Route::post('/address/submit', 'PersonalCenterController@postAddressAdd');

        //个人中心删除地址
        Route::post('/address/{id}/del', 'PersonalCenterController@postDeleteAddress');

        //个人中心优惠券
        Route::get('/discount/{type}', 'PersonalCenterController@getCouponAndCard');

        //个人中心资料管理
        Route::get('/info', 'PersonalCenterController@getInfoManagement');

        Route::post('/change_info', 'PersonalCenterController@postPersonData');
        //个人中心关于我们
        Route::get('/about', 'PersonalCenterController@getAboutMe');
        //个人中心我的积分
        Route::get('/integral', 'PersonalCenterController@getIntegral');
        Route::get('/integral_rule', 'PersonalCenterController@getIntegralRule');

        Route::get('/get_score_detail', 'PersonalCenterController@getScoreDetail');

        Route::post('/activities', 'PersonalCenterController@getJavaActivitiesData');

        //2018年01月会员专题日
        Route::get('memberLottery', 'PersonalCenterController@memberLottery'); //抽奖逻辑处理

        //2018年02月会员日专题
        Route::get('FmemberLottery', 'PersonalCenterController@FmemberLottery'); //抽奖逻辑处理

        //呼吸系统专题签到赢取积分功能
        Route::get('getScore', 'PersonalCenterController@getScore'); //获取积分逻辑处理
    });

    // 关键字搜索
    Route::get('/search/{search_key}.html', 'SearchController@getKeywordSearch');
    // 分类、按病搜索
    Route::get('/search.html', 'SearchController@getSearchList');
    // 搜索商品归集信息显示
    Route::get('/search_group_{groupId}.html', 'SearchController@getGroupList')
        ->where('groupId', '[0-9]+');
    // 品牌库
    Route::get('ppk', 'GoodsController@getBrandLibrary');
    // 品牌详情页
    Route::get('ppk/{id}.html', 'GoodsController@getBrandInfo')->where('id', '[0-9]+');
    // 通用名库
    Route::get('tyk', 'GoodsController@getPronameLibrary');
    // 通用名详情页
    Route::get('tyk/{id}.html', 'GoodsController@getPronameInfo')->where('id', '[0-9]+');

    // 问答首页
    Route::get('ask', 'ConsultativeController@askIndex');
    // 问答分类页
    Route::get('ask/{id}', 'ConsultativeController@categoryAsk')->where('id', '[0-9]+')->name('askCategory');
    // 问答关键字tag页

    // 搜索词库 http://m.800pharm.com/ck/
    Route::get('ck', 'GoodsController@getSearchibrary');
    // 搜索词详情页
    Route::get('ck/{id}.html', 'GoodsController@getSearchInfo')->where('id', '[0-9]+')->name('searchKeyWordInfo');

    Route::get('ask/{tag}', 'ConsultativeController@tagAsk')->where('tag', '[^\d]+.*')->name('askTagPage');

    // ------ 活动专区 ------

    // 母亲节活动 3440下架处理
    // Route::get('special/mothersDay/index.html', 'SpecialController@getMothersDayIndex');
    // Route::get('special/mothersDay/goods.html', 'SpecialController@getMothersDayGoods');
    // 父亲节活动
    Route::get('special/fathersDay/index.html', 'SpecialController@getFathersDayIndex');
    Route::get('special/fathersDay/goods.html', 'SpecialController@getFathersDayGoods');
    // 七月凿冰山活动 3440下架处理
    // Route::get('special/iceDay/index.html', 'SpecialController@getIceDayIndex');
    // Route::get('special/iceDay/goods.html', 'SpecialController@getIceDayGoods');
    // 八月周年庆活动
    Route::get('special/augAnniv/index.html', 'SpecialController@getAugAnnivIndex');
    // 风湿关节炎专题
    Route::get('special/augRheumatism/index.html', 'SpecialController@getAugRheumatismIndex');
    // 神经专题
    Route::get('special/augNerve/index.html', 'SpecialController@getAugNerveIndex');
    // 八月今秋收割活动
    Route::get('special/fallDay/index.html', 'SpecialController@getFallDayIndex');
    Route::get('special/fallDay/goods.html', 'SpecialController@getFallDayGoods');
    // 前列腺专题
    Route::get('special/augProstate/index.html', 'SpecialController@getAugProstateIndex');
    // 过敏专题
    Route::get('special/sepDermatology/index.html', 'SpecialController@getSepDermatologyIndex');
    // 国庆中秋专题
    Route::get('special/octNationalDay/index.html', 'SpecialController@getOctNationalDayIndex');
    // 十月会员专题
    Route::get('special/octMember/index.html', 'SpecialController@getOctMemberIndex');
    // 双十一预热一活动
    Route::get('special/double11One/index.html', 'SpecialController@getDouble11OneIndex');
    // 双十一预热二活动
    Route::get('special/double11Two/index.html', 'SpecialController@getDouble11TwoIndex');
    // 双十一主会场活动
    Route::get('special/DoubleElevenMain/index.html', 'SpecialController@getDoubleElevenMainIndex');
    // 双十一领券活动
    Route::get('special/DoubleElevenCoupon/index.html', 'SpecialController@getDoubleElevenCouponIndex');
    // 十月会员专题
    Route::get('special/novMember/index.html', 'SpecialController@getNovMemberIndex');
    // 双十一返场活动
    Route::get('special/doubleElevenBack/index.html', 'SpecialController@getDoubleElevenBackIndex');
    //双12组团红包
    Route::get('special/theDoubleTwelve/index.html', 'SpecialController@getTwelveGroupIndex');//首页
    Route::group(['prefix'=>'api/doubleTwelve'],function(){
        Route::post('sendSms','SmsController@postSendSms')->middleware(['captcha']);
        Route::get('createCouponLink','SpecialController@createCouponLink');//生成红包链接api
        Route::post('postActivityBonus','SpecialController@postActivityBonus')->middleware(['captcha','sms_code','coupon_group','loginBySms']);//领取红包
    });
    Route::get('special/theDoubleTwelve/inviting.html', 'SpecialController@getTwelveGroupInviting');//分享页面
    Route::get('special/theDoubleTwelve/inviting.html', 'SpecialController@getTwelveGroupInviting');//分享页面

    // 招行活动
    Route::get('special/bankActivity/index.html', 'SpecialController@getBankActivityIndex');
    //双12优惠券
    Route::get('special/D12Discount/index.html', 'SpecialController@getD12DiscountIndex');
    // 商家同康双12活动
    Route::get('special/theTongkangMain/index.html', 'SpecialController@getTheTongkangMainIndex');
    Route::get('special/theTongkangMain/pageOne.html', 'SpecialController@getTheTongkangMainPageOne');
    Route::get('special/theTongkangMain/pageTwo.html', 'SpecialController@getTheTongkangMainPageTwo');
    Route::get('special/theTongkangMain/pageThree.html', 'SpecialController@getTheTongkangMainPageThree');
    //同康年末活动
    Route::get('special/tongKSubject/index.html', 'SpecialController@getTongKSubjectIndex');
    // 招行活动
    Route::get('special/bankActivity/index.html', 'SpecialController@getBankActivityIndex');
    //双旦活动
    Route::get('special/shuangDan/index.html', 'SpecialController@getShuangDanIndex');
    Route::get('special/shuangDan/activity.html', 'SpecialController@getShuangDanActivity');
    // 一月会员日活动
    Route::get('special/janMember/index.html', 'SpecialController@getJanMemberIndex');
    //呼吸专题活动(3492)
    Route::get('special/breathedissertation/index.html', 'SpecialController@getbreathedissertation');
    // 普通专题入口
    Route::get('special/{name}/index.html', 'SpecialController@index');
    //会员日专题活动(3549)
    Route::get('special/Membersday/index.html', 'SpecialController@getMembersday');
});

//资讯站
Route::group(['namespace' => 'Wap', 'prefix' => 'news'], function () {
    Route::get('/', 'NewsController@index')->name('newsIndex');//首页
    Route::get('list/{cid}.html', 'NewsController@categoryList')->where('cid', '[0-9]+')->name('newsCategoryList');//二级分类列表
    // 资讯搜索
    Route::get('search.html', 'NewsController@search')->name('newsSearch');
    // 药品资讯
    Route::get('yp.html', 'NewsController@getNews')->name('yp');
    Route::get('yp_{category}.html', 'NewsController@getNews')->where('category', '[0-9]+')->name('yp_cate');
    // 疾病资讯
    Route::get('jb.html', 'NewsController@getNews')->name('jb');
    Route::get('jb_{category}.html', 'NewsController@getNews')->where('category', '[0-9]+')->name('jb_cate');
    //标签详情
    Route::get('tag/{tag_id}.html', 'NewsController@getTagDetail')->where('tag_id', '[0-9]+')->name('tagDetail');
    Route::get('tag/more_{tag_id}.html', 'NewsController@getMoreTagNews')->where('tag_id', '[0-9]+')->name('moreTagNews');
    //资讯详情
    Route::get('{news_id}.html', 'NewsController@getNewsDetail')->where('news_id', '[0-9]+')->name('newsDetail');

    //资讯点赞
    Route::post('praise-{news_id}.html','NewsController@postPraiseInformation')->where('news_id', '[0-9]+')->name('newsPraise');
    // 资讯分类
    Route::get('{classify_id}', 'NewsController@getNewsClassify')->where('classify_id', '[0-9]+')->name('newsClassify');
});


//API接口
Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {

    Route::group(['prefix' => 'v1'], function () {

        Route::group(['prefix' => 'product'], function () {
            //获取快递信息
            Route::get('getExpress', 'GoodsController@getExpress');
        });

        Route::group(['prefix' => 'cart', 'middleware' => ['auth.login_token', 'auth2']], function () {
            //添加购物车
            Route::get('add', 'CartController@getAddCart');
        });
    });

});

Route::controller('/test', 'TestController');
Route::post('wechat_jssdk', 'Wap\WechatController@getJsSdkInfo');
// Route::any('/shop/{auto_match}', 'TestController@proxyJava')->where('auto_match', '^.*$');// 开发时使用的java页面代理，以便获得登录后的session和其他的数据
Route::group(['middleware'=>'cors'],function(){
    Route::get('api/json', 'Controller@getJSON');
});
