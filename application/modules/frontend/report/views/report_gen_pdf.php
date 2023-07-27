<?php
    function remove_emoji($string)
    {//[^ -\x{2122}]\s+|\s*[^ -\x{2122}]/u
        // Match Enclosed Alphanumeric Supplement
        $regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
        $clear_string = preg_replace($regex_alphanumeric, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);
        
        // Match Supplemental Symbols and Pictographs
        $regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
        $clear_string = preg_replace($regex_supplemental, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        $regex_clear = '/[^ -\x{2122}]\s+|\s*[^ -\x{2122}]/u';
        $clear_string = preg_replace($regex_clear,'',$clear_string);

        $regex_clear_2 = '/[\x{203C}-\x{3030}]/u';
        $clear_string = preg_replace($regex_clear_2, '', $clear_string);

        $clear_string = str_replace("\xE2\x80\x8B", '', $clear_string);

        return $clear_string;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>blueeye-pdf-report</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            @font-face {
                font-family: 'THSarabunNew';
                font-style: normal;
                font-weight: bold;
                src: url("{{ public_path('application/libraries/dompdf/lib/fonts/THSarabunNew.ttf') }}") format('truetype');
            }
            @font-face {
                font-family: 'THSarabunNew-Bold';
                font-style: normal;
                font-weight: bold;
                src: url("{{ public_path('application/libraries/dompdf/lib/fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
            }
            @font-face {
                font-family: 'THSarabunNewBold';
                font-style: normal;
                font-weight: bold;
                src: url("{{ public_path('application/libraries/dompdf/lib/fonts/THSarabunNew Italic.ttf') }}") format('truetype');
            }
            @font-face {
                font-family: 'THSarabunNewBold-Italic';
                font-style: normal;
                font-weight: bold;
                src: url("{{ public_path('application/libraries/dompdf/lib/fonts/THSarabunNew BoldItalic.ttf') }}") format('truetype');
            }

            body {
                padding: 0;
                margin: 0;
                font-family: 'THSarabunNew';
            }
            header {
                position: fixed;
                top: -30;
                left: 0;
                right: 0;
                height: 50px;
                padding: 10px 0px;
                border-bottom: 1px solid #f1f1f1;
                z-index: 1000;
            }
            footer {
                position: fixed;
                bottom: -30;
                left: 0;
                right: 0;
                height: 50px;
                border-top: 1px solid #f1f1f1;
                z-index: 1000;
            }
            section {
                page-break-after: always;
                break-after: always;
                padding-top: 35%;
                text-align: center;
            }
            h1 {
                font-family: 'THSarabunNew-Bold', sans-sarif;
                display: block;
                font-size: 2em;
                margin-top: 0.67em;
                margin-bottom: 0.67em;
                margin-left: 0;
                margin-right: 0;
                font-weight: bold;
            }
            h2 {
                font-family: 'THSarabunNew-Bold', sans-sarif;
                display: block;
                font-size: 1.5em;
                margin-top: 0.83em;
                margin-bottom: 0.83em;
                margin-left: 0;
                margin-right: 0;
                font-weight: bold;
            }
            h3 {
                font-family: 'THSarabunNew-Bold', sans-sarif;
                display: block;
                font-size: 1.17em;
                margin-top: 1em;
                margin-bottom: 1em;
                margin-left: 0;
                margin-right: 0;
                font-weight: bold;
            }
            h4 {
                font-family: 'THSarabunNew-Bold', sans-sarif;
                display: block;
                font-size: 1em;
                margin-top: 1.33em;
                margin-bottom: 1.33em;
                margin-left: 0;
                margin-right: 0;
                font-weight: bold;
            }
            h5 {
                font-family: 'THSarabunNew-Bold', sans-sarif;
                display: block;
                font-size: .83em;
                margin-top: 1.67em;
                margin-bottom: 1.67em;
                margin-left: 0;
                margin-right: 0;
                font-weight: bold;
            }
            .topic-style {
                display: inline;
                font-family: 'THSarabunNew-Bold', sans-sarif;
                font-size: 20px;
                font-weight: bold;
            }
            .tab-space {
                tab-size: ;
            }
            .cover-page-daterange {
                font-family: 'THSarabunNew', monospace;
                display: block;
                font-size: 18px;
                margin-top: 1.67em;
                margin-bottom: 1.67em;
                margin-left: 0;
                margin-right: 0;
            }
            .clear-top-space {
                margin-top: -10px;
            }
            .page-break-content {
                page-break-inside: avoid;
            }
            .topic-top-space {
                margin-top: 48px;
                margin-bottom: 15px;
            }
            .topic-icon-sizing {
                width: 20px;
                height: 20px;
                padding-bottom: -3px;
            }
            .content-icon-sizing {
                width: 15px;
                height: 15px;
            }
            .after-header .left-header, .right-header {
                font-size: 18px;
                font-weight: bold;
                color: #0483FF;
            }
            .left-header {
                float: left;
            }
            .right-header {
                float: right;
            }
            .after-header {
                height: 30px;
                padding: 10px 0px;
            }

            /* table style */
            table {
                width: 100%;
            }
            th {
                padding: 5px;
            }
            .text-left {
                text-align: left;
            }
            .text-right {
                text-align: right;
            }
            .text-center {
                text-align: center;
            }
            .text-vertical-middle {
                vertical-align: middle;
            }
            .th-text-bold {
                font-family: 'THSarabunNew-Bold', sans-sarif;
                font-size: 16px;
                font-weight: bold;
            }
            .td-text-bold {
                font-family: 'THSarabunNew', sans-sarif;
                color: #8e8e8e;
                font-size: 16px;
            }
            .td-mentions {
                font-family: 'THSarabunNew-Bold', sans-sarif;
                font-size: 18px;
                color: #0483FF;
            }
            .td-volume-of-mentions {
                font-family: 'THSarabunNew';
                font-size: 16px;
            }
            .td-table-group-keyword {
                padding: 3px;
                font-size: 16px;
            }
            .border {
                /* border: 1px solid #000000; */
                border-collapse: collapse;
            }
            .border-solid thead > tr > th {
                border-bottom: 1px solid #000000;
                text-align: center;
                vertical-align: middle;
            }
            .border-solid tbody > tr > td {
                /* border-left: 1px solid #000000;
                border-right: 1px solid #000000; */
                text-align: center;
                vertical-align: middle;
            }
            .border-solid tbody:last-child {
                /* border-bottom: 1px solid #000000; */
            }
            .rows tr:nth-child(even) {background-color: #eeeeee}
            .rows tr:nth-child(odd) {background-color: #ffffff}
            
        </style>
    </head>
    <body>
        <section>
            <h1><?php echo $client['company_name']; ?></h1>
            <br><br>
            <h3>DATE RANGE</h3>
            <div class="clear-top-space cover-page-daterange">
                <?php echo $dateRange; ?>
            </div>
        </section>
        <header>
            <div class="after-header">
                <div class="left-header">
                    <p><?php echo $client['company_name']; ?></p>
                </div>
                <div class="right-header">
                    <p><?php echo $dateRange; ?></p>
                </div>
            </div>
        </header>
        <main>
            <!-- Summary of mentions --> 
            <?php if (!empty($summaryOfMentions)) { ?>
            <div class="page-break-content">
                <div class="topic-top-space">
                    <div style="height: 20px; line-height: 20px; display: flex;">
                        <img src="themes/default/assets/images/interface/document.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Summary of mentions<pre></h3>
                    </div>
                </div>
                <table border="0" cellspacing="0px" cellpadding="0px">
                    <tbody>
                        <tr>
                            <td rowspan="2"><img src="themes/default/assets/images/interface/diagram.png" class="content-icon-sizing"></td>
                            <td class="td-text-bold"><b>VOLUME OF MENTIONS</b></td>
                            <td rowspan="2"><img src="themes/default/assets/images/interface/wifi.png" class="content-icon-sizing"></td>
                            <td class="td-text-bold"><b>SOCIAL MEDIA REACH</b></td>
                            <td rowspan="2"><img src="themes/default/assets/images/interface/network.png" class="content-icon-sizing"></td>
                            <td class="td-text-bold"><b>NON SOCIAL MEDIA REACH</b></td>
                            <td rowspan="2"><img src="themes/default/assets/images/interface/smile.png" class="content-icon-sizing"></td>
                            <td class="td-text-bold"><b>POSITIVE</b></td>
                            <td rowspan="2"><img src="themes/default/assets/images/interface/sad.png" class="content-icon-sizing"></td>
                            <td class="td-text-bold"><b>NEGATIVE</b></td>
                        </tr>
                        <tr class="td-mentions">
                            <td><?php echo $summaryOfMentions['volum']['mentionCurrent']; ?></td>
                            <td><?php echo $summaryOfMentions['social']['SM']; ?></td>
                            <td>
                                <?php echo $summaryOfMentions['nonsocial']['WB'] + $summaryOfMentions['nonsocial']['NW'] ?>
                            </td>
                            <td><?php echo $summaryOfMentions['positive']['Positive_row'] ?></td>
                            <td><?php echo $summaryOfMentions['negative']['Negative_row'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php } ?>
            <?php if (!empty($volumeOfMentions)) { ?>
            <div class="page-break-content">
                <div class="topic-top-space">
                    <div style="height: 20px; line-height: 20px; display: flex;">
                        <img src="themes/default/assets/images/interface/diagram.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Volume of mentions graph</h3>
                    </div>
                </div>
                <div style="text-align: center;">
                    <img alt="" width="100%" height="300" src="<?php echo $volumeOfMentions['img'] ?>">
                    <table style="width: 70%;" border="0" >
                        <tbody style="vertical-align: middle; text-align: center;">
                            <tr>
                                <td class="td-text-bold"><b>CURRENT PERIOD:</b></td>
                                <td><img src="themes/default/assets/images/interface/up-arrow.png" class="content-icon-sizing" style="padding-bottom: -5px;"></td>
                                <td class="td-volume-of-mentions"><?php echo $volumeOfMentions['maxValue'] ?></td>
                                <td class="td-text-bold"><b>MAX.</b></td>
                                <td><img src="themes/default/assets/images/interface/down-arrow.png" class="content-icon-sizing" style="padding-bottom: -5px;"></td>
                                <td class="td-volume-of-mentions"><?php echo $volumeOfMentions['minValue'] ?></td>
                                <td class="td-text-bold"><b>MIN.</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
            <?php if (!empty($socialMediaReachGraph)) { ?>
            <div class="page-break-content">
                <div class="topic-top-space">
                    <div style="height: 20px; line-height: 20px; display: flex;">
                        <img src="themes/default/assets/images/interface/wifi.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Social media reach graph</h3>
                    </div>
                </div>
                <div style="text-align: center;">
                    <img alt="" width="100%" height="300" src="<?php echo $socialMediaReachGraph['img'] ?>">
                    <table style="width: 70%;" border="0">
                        <tbody style="vertical-align: middle; text-align: center;">
                            <tr>
                                <td class="td-text-bold"><b>CURRENT PERIOD:</b></td>
                                <td><img src="themes/default/assets/images/interface/up-arrow.png" class="content-icon-sizing" style="padding-bottom: -5px;"></td>
                                <td class="td-volume-of-mentions"><?php echo $socialMediaReachGraph['maxValue'] ?></td>
                                <td class="td-text-bold"><b>MAX.</b></td>
                                <td><img src="themes/default/assets/images/interface/down-arrow.png" class="content-icon-sizing" style="padding-bottom: -5px;"></td>
                                <td class="td-volume-of-mentions"><?php echo $socialMediaReachGraph['minValue'] ?></td>
                                <td class="td-text-bold"><b>MIN.</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
            <?php if (!empty($nonSocialMediaReachGraph)) { ?>
            <div class="page-break-content">
                <div class="topic-top-space">
                    <div style="height: 20px; line-height: 20px; display: flex;">
                        <img src="themes/default/assets/images/interface/network.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Non Social media reach graph</h3>
                    </div>
                </div>
                <div style="text-align: center;">
                    <img alt="" width="100%" height="300" src="<?php echo $nonSocialMediaReachGraph['img'] ?>">
                    <table style="width: 70%;" border="0">
                        <tbody style="vertical-align: middle; text-align: center;">
                            <tr>
                                <td class="td-text-bold"><b>CURRENT PERIOD:</b></td>
                                <td><img src="themes/default/assets/images/interface/up-arrow.png" class="content-icon-sizing" style="padding-bottom: -5px;"></td>
                                <td class="td-volume-of-mentions"><?php echo $nonSocialMediaReachGraph['maxValue'] ?></td>
                                <td class="td-text-bold"><b>MAX.</b></td>
                                <td><img src="themes/default/assets/images/interface/down-arrow.png" class="content-icon-sizing" style="padding-bottom: -5px;"></td>
                                <td class="td-volume-of-mentions"><?php echo $nonSocialMediaReachGraph['minValue'] ?></td>
                                <td class="td-text-bold"><b>MIN</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
            <?php if (!empty($mentionsPerCategory)) { ?>
                <div class="page-break-content">
                    <div class="topic-top-space">
                        <div style="height: 20px; line-height: 20px; display: flex;">
                            <img src="themes/default/assets/images/interface/chat-bubbles.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Mention per category</h3>
                        </div>
                    </div>
                    <table class="border" border="0" style="padding: 10px;">
                        <tbody>
                            <tr>
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-right-style: none; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <img src="themes/default/assets/images/interface/facebook-color.png" style="width: 25px; height: 25px;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions text-vertical-middle" style="border-top: 1px solid #000000;"><?php echo $mentionsPerCategory['count']['fb'];?></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">
                                    <div style="width: 50px">
                                        <img style="width: 100%;" src="<?php echo $mentionsPerCategory['img'][0];?>">
                                    </div>
                                </td>
                                <!-- end graph image -->
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-right-style: none; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <img src="themes/default/assets/images/interface/twitter-color.png" style="width: 25px; height: 25px;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions text-vertical-middle" style="border-top: 1px solid #000000;"><?php echo $mentionsPerCategory['count']['tw'];?></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">
                                    <div style="width: 50px">
                                        <img style="width: 100%;" src="<?php echo $mentionsPerCategory['img'][1];?>">
                                    </div>
                                </td>
                                <!-- end graph image -->
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-right-style: none; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <img src="themes/default/assets/images/interface/youtube-color.png" style="width: 25px; height: 25px;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions text-vertical-middle" style="border-top: 1px solid #000000;"><?php echo $mentionsPerCategory['count']['yt'];?></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">
                                    <div style="width: 50px">
                                        <img style="width: 100%;" src="<?php echo $mentionsPerCategory['img'][2];?>">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-text-bold" style="border-bottom: 1px solid #000000; padding-bottom: 5px;"><b>FACEBOOK</b></td>
                                <td class="td-text-bold" style="border-bottom: 1px solid #000000; padding-bottom: 5px;"><b>TWITTER</b></td>
                                <td class="td-text-bold" style="border-bottom: 1px solid #000000; padding-bottom: 5px;"><b>YOUTUBE</b></td>
                            </tr>
                            <tr>
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-right-style: none; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <img src="themes/default/assets/images/interface/news-color.png" style="width: 25px; height: 25px;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions text-vertical-middle" style="border-top: 1px solid #000000;"><?php echo $mentionsPerCategory['count']['nw'];?></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">
                                    <div style="width: 50px">
                                        <img style="width: 100%;" src="<?php echo $mentionsPerCategory['img'][3];?>">
                                    </div>
                                </td>
                                <!-- end graph image -->
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-right-style: none; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <img src="themes/default/assets/images/interface/webboard-color.png" style="width: 25px; height: 25px;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions text-vertical-middle" style="border-top: 1px solid #000000;"><?php echo $mentionsPerCategory['count']['pt'];?></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">
                                    <div style="width: 50px">
                                        <img style="width: 100%;" src="<?php echo $mentionsPerCategory['img'][4];?>">
                                    </div>
                                </td>
                                <!-- end graph image -->
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <img src="themes/default/assets/images/interface/instagram-color.png" style="width: 25px; height: 25px;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions text-vertical-middle" style="border-top: 1px solid #000000;"><?php echo $mentionsPerCategory['count']['ig'];?></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">
                                    <div style="width: 50px">
                                        <img style="width: 100%;" src="<?php echo $mentionsPerCategory['img'][5];?>">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-text-bold" style="border-bottom: 1px solid #000000; padding-bottom: 5px;"><b>NEWS</b></td>
                                <td class="td-text-bold" style="border-bottom: 1px solid #000000; padding-bottom: 5px;"><b>WEBBOARD</b></td>
                                <td class="td-text-bold" style="border-bottom: 1px solid #000000; padding-bottom: 5px;"><b>INSTAGRAM</b></td>
                            </tr>
                            <tr>
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <img src="themes/default/assets/images/interface/tik-tok-color.png" style="width: 25px; height: 25px;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions text-vertical-middle" style="border-top: 1px solid #000000;"><?php echo $mentionsPerCategory['count']['tt'];?></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">
                                    <div style="width: 50px">
                                        <img style="width: 100%;" src="<?php echo $mentionsPerCategory['img'][6];?>">
                                    </div>
                                </td>
                                <!-- end graph image -->
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <img src="themes/default/assets/images/interface/blockdit-color.png" style="width: 25px; height: 25px;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions text-vertical-middle" style="border-top: 1px solid #000000;"><?php echo $mentionsPerCategory['count']['bd'];?></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <div style="width: 50px">
                                        <img style="width: 100%;" src="<?php echo $mentionsPerCategory['img'][7];?>">
                                    </div>
                                </td>
                                <!-- end graph image -->
                                <!-- icon -->
                                <td rowspan="2" class="text-center" style="padding: 5px; border-top: 1px solid #000000; border-left: 1px solid #000000;">
                                </td>
                                <!-- end icon -->
                                <!-- count -->
                                <td class="td-mentions" style="border-top: 1px solid #000000;"></td>
                                <!-- end count -->
                                <!-- graph image -->
                                <td rowspan="2" style="border-top: 1px solid #000000;">
                                    <div style="width: 50px">
                                    </div>
                                </td>
                                <!-- end graph image -->
                            </tr>
                            <tr>
                                <td class="td-text-bold" style="border-bottom: 1px solid #000000; padding-bottom: 5px;"><b>TIKTOK</b></td>
                                <td class="td-text-bold" style="border-bottom: 1px solid #000000; padding-bottom: 5px;"><b>BLOCKDIT</b></td>
                                <td class="td-text-bold"><b></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
            <?php if (!empty($tableGroupKeywordList)) { ?>
                <div class="page-break-content">
                    <div class="topic-top-space">
                        <div style="height: 20px; line-height: 20px; display: flex;">
                            <img src="themes/default/assets/images/interface/table.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Table group keyword list</h3>
                        </div>
                    </div>
                    <table class="border border-solid" border="0">
                        <thead>
                            <tr class="th-text-bold">
                                <th style="vertcal-align: middle; padding-top: -5px;"><b>Group Keyword Name</b></th>
                                <th><img src="themes/default/assets/images/interface/facebook-color.png" width="20" height="20"></th>
                                <th><img src="themes/default/assets/images/interface/twitter-color.png" width="20" height="20"></th>
                                <th><img src="themes/default/assets/images/interface/youtube-color.png" width="20" height="20"></th>
                                <th><img src="themes/default/assets/images/interface/news-color.png" width="20" height="20"></th>
                                <th><img src="themes/default/assets/images/interface/webboard-color.png" width="20" height="20"></th>
                                <th><img src="themes/default/assets/images/interface/instagram-color.png" width="20" height="20"></th>
                                <th><img src="themes/default/assets/images/interface/tik-tok-color.png" width="20" height="20"></th>
                                <th><img src="themes/default/assets/images/interface/blockdit-color.png" width="20" height="20"></th>
                                <th style="vertcal-align: middle; padding-top: -5px;">Total</th>
                            </tr>
                        </thead>
                        <tbody id="tableGraph_data" class="rows">
                            <?php for ($i=0; $i < count($tableGroupKeywordList['sum']); $i++) { ?>
                                <tr class="td-table-group-keyword">
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['name'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['Facebook'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['Twitter'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['Youtube'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['News'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['Webboard'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['Instagram'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['Tiktok'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['Blockdit'];?></td>
                                    <td style="padding-top: -5px;"><?php echo $tableGroupKeywordList['sum'][$i]['Total'];?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
            <?php if (!empty($graphSentimentMonitoringAnalysis)) { ?>
                <div class="page-break-content">
                    <div class="topic-top-space">
                        <div style="height: 20px; line-height: 20px; display: flex;">
                            <img src="themes/default/assets/images/interface/bar-chart.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Sentiment graph</h3>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <img alt="" width="100%" height="300" src="<?php echo $graphSentimentMonitoringAnalysis['img'] ?>">
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($topUser)) { ?>
                <div class="page-break-content">
                    <div class="topic-top-space">
                        <div style="height: 20px; line-height: 20px; display: flex;">
                            <img src="themes/default/assets/images/interface/user.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Top user</h3>
                        </div>
                    </div>
                    <table class="border border-solid" border="0">
                        <thead>
                            <tr class="th-text-bold">
                                <th style="vertcal-align: middle; padding-top: -5px; width: 20px;">No</th>
                                <th style="vertcal-align: middle; padding-top: -5px; width: 50px;">Source</th>
                                <th style="vertcal-align: middle; padding-top: -5px;">Name</th>
                                <th style="vertcal-align: middle; padding-top: -5px;">Reach</th>
                            </tr>
                        </thead>
                        <tbody id="tableGraph_data" class="rows">
                            <?php for ($i=0; $i < count($topUser); $i++) { ?>
                                <tr>
                                    <td rowspan="1" style="padding-top: -5px; width: 20px;"><?php echo $i+1;?></td>
                                    <?php if ($topUser[$i]['sourceid'] == 1) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/facebook-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topUser[$i]['sourceid'] == 2) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/twitter-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topUser[$i]['sourceid'] == 3) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/youtube-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topUser[$i]['sourceid'] == 4) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/news-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topUser[$i]['sourceid'] == 5) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/webboard-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topUser[$i]['sourceid'] == 6) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/instagram-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topUser[$i]['sourceid'] == 7) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/tik-tok-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topUser[$i]['sourceid'] == 9) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/line-color.png" width="20" height="20"></td>
                                    <?php }?>
                                    <td style="padding-top: -5px;"><?php echo $topUser[$i]['post_name']?></td>
                                    <td rowspan="1" style="padding-top: -5px; width: 50px;"><?php echo $topUser[$i]['count_post']?></td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
            <?php  if (!empty($topShare)) { ?>
                <div class="page-break-content">
                    <div class="topic-top-space">
                        <div style="height: 20px; line-height: 20px; display: flex;">
                            <img src="themes/default/assets/images/interface/share.png" class="topic-icon-sizing"><h3 class="topic-style" style="margin-left: 30px;">Top share</h3>
                        </div>
                    </div>
                    <table class="border border-solid" border="0">
                        <thead>
                            <tr class="th-text-bold">
                                <th style="vertcal-align: middle; padding-top: -5px; width: 20px;">No</th>
                                <th style="vertcal-align: middle; padding-top: -5px; width: 50px;">Source</th>
                                <th style="vertcal-align: middle; padding-top: -5px;">Topic</th>
                                <th style="vertcal-align: middle; padding-top: -5px;">Reach</th>
                            </tr>
                        </thead>
                        <tbody id="tableGraph_data" class="rows">
                            <?php for ($i=0; $i < count($topShare); $i++) { ?>
                                <tr>
                                    <td rowspan="1" style="padding-top: -5px; width: 20px;"><?php echo $i+1;?></td>
                                    <?php if ($topShare[$i]['sourceid'] == 1) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/facebook-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topShare[$i]['sourceid'] == 2) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/twitter-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topShare[$i]['sourceid'] == 3) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/youtube-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topShare[$i]['sourceid'] == 4) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/news-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topShare[$i]['sourceid'] == 5) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/webboard-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topShare[$i]['sourceid'] == 6) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/instagram-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topShare[$i]['sourceid'] == 7) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/tik-tok-color.png" width="20" height="20"></td>
                                    <?php } elseif ($topShare[$i]['sourceid'] == 9) { ?>
                                        <td style="width: 50px;"><img src="themes/default/assets/images/interface/line-color.png" width="20" height="20"></td>
                                    <?php }?>
                                    <td style="padding-top: -5px;"><?php echo remove_emoji($topShare[$i]['post_detail']);?></td>
                                    <td rowspan="1" style="padding-top: -5px; width: 50px;"><?php echo $topShare[$i]['count_share'];?></td>
                                </tr>
                                <!-- <tr>
                                    <td class="tdclass " style="border-bottom: 1px solid #EFEFEF;"><?php// echo $topShare[$i]['post_link'];?></td>
                                </tr> -->
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </main>
        <footer></footer>
    </body>
</html>