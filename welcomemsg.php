<?php
function welcome_msg()
{
    if (is_bot()) {
        return;
    }
    if ($m = apply_filters('welcome_msg', $string)) {
        return $m;
        return;
    }
    global $referer;
    $referer  = $_SERVER['HTTP_REFERER'];
    $hostinfo = parse_url($referer);
    $host_h   = $hostinfo["host"];
    $host_p   = $hostinfo["path"];
    $host     = array(
        $host_h,
        $host_p
    );
    if (substr($host_h, 0, 4) == 'www.')
        $host_h = substr($host_h, 4);
    $host_h_url = '<a href="http://' . $host_h . '/">$host_h</a>';
    if ($referer == "") {
        $callback = "<!--您直接访问了本站!-->\n";
        if ($_COOKIE["comment_author_" . COOKIEHASH] != "") {
            $callback = 'Howdy, <strong>' . $_COOKIE["comment_author_" . COOKIEHASH] . '</strong>, 欢迎回来';
        } else {
            $callback = "您直接访问了本站!  莫非您记住了我的<strong>域名</strong>.厉害~  我倍感荣幸啊 嘿嘿";
        }
        //搜索引擎
        //baidu
    } elseif (preg_match('/baidu.*/i', $host_h)) {
        $callback = '您通过 <strong>百度</strong> 找到了我，厉害！';
        //360
    } elseif (preg_match('/haosou.*/i', $host_h)) {
        $callback = '您通过 <strong>好搜</strong> 找到了我，厉害！';
        //google
    } elseif (!preg_match('/www\.google\.com\/reader/i', $referer) && preg_match('/google\./i', $referer)) {
        $callback = '您居然通过 <strong>Google</strong> 找到了我! 一定是个技术宅吧!';
        //yahoo
    } elseif (preg_match('/search\.yahoo.*/i', $referer) || preg_match('/yahoo.cn/i', $referer)) {
        $callback = '您通过 <strong>Yahoo</strong> 找到了我! 厉害！';
        //阅读器
        //google
    } elseif (preg_match('/google\.com\/reader/i', $referer)) {
        $callback = "感谢你通过 <strong>Google</strong> 订阅我!  既然过来读原文了. 欢迎留言指导啊.嘿嘿 ^_^";
        //xianguo
    } elseif (preg_match('/xianguo\.com\/reader/i', $referer)) {
        $callback = "感谢你通过 <strong>鲜果</strong> 订阅我!  既然过来读原文了. 欢迎留言指导啊.嘿嘿 ^_^";
        //zhuaxia
    } elseif (preg_match('/zhuaxia\.com/i', $referer)) {
        $callback = "感谢你通过 <strong>抓虾</strong> 订阅我!  既然过来读原文了. 欢迎留言指导啊.嘿嘿 ^_^";
        //哪吒
    } elseif (preg_match('/inezha\.com/i', $referer)) {
        $callback = "感谢你通过 <strong>哪吒</strong> 订阅我!  既然过来读原文了. 欢迎留言指导啊.嘿嘿 ^_^";
        //有道
    } elseif (preg_match('/reader\.youdao/i', $referer)) {
        $callback = "感谢你通过 <strong>有道</strong> 订阅我!  既然过来读原文了. 欢迎留言指导啊.嘿嘿 ^_^";
        //自己  
    } elseif (self()) { //若来路是自己的网站
        //$callback = "你在找什么呢？试试上面的搜索吧~"."\n";
        $callback = false;
    } elseif ($_COOKIE["comment_author_" . COOKIEHASH] != "") {
        $callback = 'Howdy, <strong>' . $_COOKIE["comment_author_" . COOKIEHASH] . '</strong>欢迎从<strong>' . $host_h . '</strong>回来';
    } else {
        $callback = '欢迎来自<strong>' . $host_h . '</strong>的朋友. 我经常分享一些好东西哦 ^_^ ';
    }
    return $callback;
}
//判断来路是自己网站的函数
function self()
{
    $local_info = parse_url(get_option('siteurl'));
    $local_host = $local_info['host'];
    //check self
    if (preg_match("/^http:\/\/(\w+\.)?($local_host)/", $_SERVER['HTTP_REFERER']) != 0)
        return true;
}
/**
 * 针对ie不同版本设置不同的cookie
 *
 * 为了后面的推送升级通知
 */
function setcookie_for_ie()
{
    if (isset($_COOKIE['alert_ie_visitor_' . COOKIEHASH]))
        return;
    if (preg_match('/MSIE\s6/i', $_SERVER['HTTP_USER_AGENT'])) {
        //对于使用古老版ie用频繁推送 (cookies 5分钟失效)
        setcookie('alert_ie_visitor_' . COOKIEHASH, 'ie6', time() + (20), COOKIEPATH, COOKIE_DOMAIN);
    } elseif (preg_match('/MSIE\s7/i', $_SERVER['HTTP_USER_AGENT'])) {
        //对于使用ie7的用户减少推送 (cookies 3天失效)
        setcookie('alert_ie_visitor_' . COOKIEHASH, 'ie7', time() + (60 * 60 * 24 * 3), COOKIEPATH, COOKIE_DOMAIN);
    } elseif (preg_match('/MSIE\s8/i', $_SERVER['HTTP_USER_AGENT'])) {
        //对于使用ie8的用尽量不要推送 (cookies 100天失效)
        setcookie('alert_ie_visitor_' . COOKIEHASH, 'ie8', time() + (60 * 60 * 24 * 10), COOKIEPATH, COOKIE_DOMAIN);
    }
}
/**
 * 分析浏览器 对于使用IE老版本的用户推送提醒
 * 不要过分推送, 根据cookie判断
 * 比如 对IE6的推送! 我希望是每隔20秒要有一次!
 * @see setcookie_for_ie()
 */
function killIE($msg)
{
    if (preg_match('/MSIE\s6/i', $_SERVER['HTTP_USER_AGENT'])) {
        if (!$_COOKIE['alert_ie_visitor_' . COOKIEHASH]) {
            $msg .= '<p>呃~ , 我不得不再提示一下:</p>';
            $msg .= '<p>您正在使用古老的 Internet Explorer 浏览网页, 该浏览器不符合W3C国际标准, 本站网页可能显示不正常,或部分功能无法使用<br/>如果您<strong><a rel="nofollow" title="ie8" href="http://www.microsoft.com/windows/internet-explorer/">升级到 Internet Explorer 8</a></strong> 或<strong>转换到另一个浏览器</strong>, 本站将能为您提供更好的服务. </p>向您<strong>推荐: </strong><br/>速度最快的 <strong><a rel="nofollow" title="chrome" href="http://www.google.com/chrome/">Chrome</a></strong> 和定制性最强的 <strong><a rel="nofollow" title="firefox" href="http://www.mozilla.com/">Firefox</a></strong>';
            //add_action('init', 'setcookie_for_alert_ie_visitor');
        }
    } elseif (preg_match('/MSIE\s7/i', $_SERVER['HTTP_USER_AGENT'])) {
        if (!$_COOKIE['alert_ie_visitor_' . COOKIEHASH]) {
            $msg .= '<p>呃~ , 顺便提示一下:</p>';
            $msg .= '<p>您正在使用旧版本的 Internet Explorer 版本浏览网页，如果您<strong><a rel="nofollow" title="ie8" href="http://www.microsoft.com/windows/internet-explorer/">升级到 Internet Explorer 8</a></strong> 或<strong>转换到另一个浏览器</strong>, 本站将能为您提供更好的服务. </p>向您<strong>推荐: </strong><br/>速度最快的 <strong><a rel="nofollow" title="chrome" href="http://www.google.com/chrome/">Chrome</a></strong> 和定制性最强的 <strong><a rel="nofollow" title="firefox" href="http://www.mozilla.com/">Firefox</a></strong>';
        }
    } elseif (preg_match('/MSIE\s8/i', $_SERVER['HTTP_USER_AGENT'])) {
        if (!$_COOKIE['alert_ie_visitor_' . COOKIEHASH]) {
            $msg .= '<p>呃~ , 顺便提示一下:</p>';
            $msg .= '<p>很高兴看到你使用较高版本的 Internet Explorer 浏览器! 但是我还是要向您<strong>推荐: </strong><br/>速度最快的 <strong><a rel="nofollow" title="chrome" href="http://www.google.com/chrome/">Chrome</a></strong> 和定制性最强的 <strong><a rel="nofollow" title="firefox" href="http://www.mozilla.com/">Firefox</a></strong> </p>';
        }
    } else {
        return;
    }
    return $msg;
}
/**
 * 通过USER_Agent判断是否为机器人.
 */
function is_bot(){
    $bots = array('Google Bot1' => 'googlebot', 'Google Bot2' => 'google', 'MSN' => 'msnbot', 'Alex' => 'ia_archiver', 'Lycos' => 'lycos', 'Ask Jeeves' => 'jeeves', 'Altavista' => 'scooter', 'AllTheWeb' => 'fast-webcrawler', 'Inktomi' => 'slurp@inktomi', 'Turnitin.com' => 'turnitinbot', 'Technorati' => 'technorati', 'Yahoo' => 'yahoo', 'Findexa' => 'findexa', 'NextLinks' => 'findlinks', 'Gais' => 'gaisbo', 'WiseNut' => 'zyborg', 'WhoisSource' => 'surveybot', 'Bloglines' => 'bloglines', 'BlogSearch' => 'blogsearch', 'PubSub' => 'pubsub', 'Syndic8' => 'syndic8', 'RadioUserland' => 'userland', 'Gigabot' => 'gigabot', 'Become.com' => 'become.com','Bot'=>'bot','Spider'=>'spider','yinheli_for_test'=>'dFirefox');
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    foreach ($bots as $name => $lookfor) {
        if (stristr($useragent, $lookfor) !== false) {
            return true;
            break;
        }
    }
}

?>