$(document).ready(function() {
    $('.wrapper').height(window.innerHeight);
    document.addEventListener("fullscreenchange", function(e) {
        $('.wrapper').height(window.innerHeight);
    });
    $('input[name="mobile"]').on('input propertychange', function() {

        if ($(this).val() != '') {
            $('.cancel').show();
        } else {
            $('.cancel').hide();
        }
    });

    $('.cancel').click(function() {
        $('input[name="mobile"]').val('');
        $('input[name="mobile"]').focus();
        $('.cancel').hide();
    });

    //发送短信
    $('.sms').click(function() {
        var self = $(this);
        if (self.hasClass('active')) {
            return false;
        }
        var mobile = $('input[name="mobile"]').val();
        if (!mobile) {
            layer.open({
                content: '请输入手机号！',
                skin: 'msg',
                time: 2 //2秒后自动关闭
            });
            return false;
        }
        $.post("/v1/public/sendSms", 'mark=signUp&mobile=' + mobile, function(res) {
            layer.open({
                content: res.message,
                skin: 'msg',
                time: 2
            });
            if (res.code == 0) {
                self.addClass('active');
                timeOut(60);
            }
        });
    });

    //密码
    $('.pass').click(function() {
        if ($(this).hasClass('eye')) {
            $('input[name="password"]').attr('type', 'password');
            $(this).removeClass('eye').addClass('uneye');
        } else {
            $('input[name="password"]').attr('type', 'text');
            $(this).removeClass('uneye').addClass('eye');
        }
    });

    //注册
    $('#submit').click(function() {
        if ($('#submit').hasClass('active')) {
            return false;
        }
        if (!$('input[name="mobile"]').val()) {
            layer.open({
                content: '请输入您的手机号',
                skin: 'msg',
                time: 2
            });
            return false;
        }
        if (!$('input[name="code"]').val()) {
            layer.open({
                content: '请输入手机验证码',
                skin: 'msg',
                time: 2
            });
            return false;
        }
        if (!$('input[name="password"]').val()) {
            layer.open({
                content: '请输入密码',
                skin: 'msg',
                time: 2
            });
            return false;
        }
        $('#submit').addClass('active');
        $('input').attr('readonly', 'readonly');
        $.post('/v1/public/signUp', $('form').serialize(), function(res) {
            layer.open({
                content: res.message,
                skin: 'msg',
                time: 2,
                end: function(index) {
                    //注册成功
                    if (res.code == 0) {
                        $('.header').slideUp('300');
                        $('.form').slideUp('400');
                        $('.success').slideDown('1000');
                        $('input').val('');
                    }
                    $('#submit').removeClass('active');
                    $('input').removeAttr('readonly');
                }
            });
        });
    });


    $('#download').click(function(event) {
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        var ios = 'https://itunes.apple.com/WebObjects/MZStore.woa/wa/viewSoftware?id=1197311166&mt=8';
        var andriod = 'http://fir.im/neby';
        if (isiOS) {
            window.location.href = ios;
        } else {
            window.location.href = andriod;
        }
    });
});


//倒计时
var timeOut = function(time) {
    var sms = $('.sms');
    $('.sms').text(time + 's后重发');
    if (time > 0) {
        setTimeout(function() {
            timeOut(time - 1);
        }, 1000);
    } else {
        sms.removeClass('active');
        sms.text('获取验证码');
    }
}

//判断是否微信登陆
function isWeiXin() {
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
}