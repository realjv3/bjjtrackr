<!DOCTYPE html>
<html  style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }
        img {
            border: 0 !important;
            outline: none !important;
        }
        p {
            Margin: 0px !important;
            Padding: 0px !important;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0px;
            mso-table-rspace: 0px;
        }
        td, a, span {
            border-collapse: collapse;
            mso-line-height-rule: exactly;
        }
    </style>
</head>
<body itemscope itemtype="http://schema.org/EmailMessage" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="em_full_wrap" align="center"  bgcolor="#efefef">
    <tr>
        <td align="center" valign="top">
            <table align="center" width="650" border="0" cellspacing="0" cellpadding="0" class="em_main_table" style="width:650px; table-layout:fixed;">
                <tr>
                    <td align="center" valign="top" style="padding:0 25px;" class="em_aside10">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td height="26" style="height:26px;" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <a href="https://flowrolled.com" target="_blank" style="text-decoration:none;">
                                        <img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/High%20Resolution%20Logo.png" alt="flowrolled" border="0" style="display:block; font-family:Arial, sans-serif; font-size:18px; line-height:25px; text-align:center; color:#1d4685; font-weight:bold; max-width:608px;" class="em_w150" />
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td height="28" style="height:28px;" class="em_h20">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="em_full_wrap" align="center" bgcolor="#efefef">
    <tr>
        <td align="center" valign="top" class="em_aside5">
            <table align="center" width="650" border="0" cellspacing="0" cellpadding="0" class="em_main_table" style="width:650px; table-layout:fixed;">
                <tr>
                    <td align="center" valign="top" style="padding:0 25px; background-color:#ffffff;" class="em_aside10">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td height="25" style="height:25px;" class="em_h10">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_blue em_font_22" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 26px; line-height: 29px; color:#264780; font-weight:bold;">Thank You For Your Payment</td>
                            </tr>
                            <tr>
                                <td height="15" style="height:15px; font-size:0px; line-height:0px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 16px; line-height: 22px; color:#434343;">
                                    Thank you very much for choosing Flowrolled for your Jiu Jitsu academy management needs.
                                </td>
                            </tr>
                            <tr>
                                <td height="15" style="height:15px; font-size:1px; line-height:1px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 16px; line-height: 22px; color:#434343;">
                                    <strong>Invoice Date:</strong> {{ date("F j, Y", $invoice->period_end)}}
                                </td>
                            </tr>
                            <tr>
                                <td height="20" style="height:20px; font-size:1px; line-height:1px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="40" style="height:40px;" class="em_h10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="15" class="em_h10" style="height:15px; font-size:1px; line-height:1px;">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" valign="top" style="padding:0 50px; background-color:#ffffff;" class="em_aside10">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td height="35" style="height:35px;" class="em_h10">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 18px; line-height: 22px; color:#434343; font-weight:bold;">BILLED TO:</td>
                            </tr>
                            <tr>
                                <td height="10" style="height:10px; font-size:1px; line-height:1px;">&nbsp;</td>
                            </tr>

                            <tr>
                                <td class="em_grey" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 16px; line-height: 24px; color:#434343;">
                                    {{$client->name}}<br />
                                    {{$client->address1}}&nbsp;{{$client->address2}}<br />
                                    {{$client->city}},&nbsp;{{$client->state}}&nbsp;{{$client->zip}}
                                </td>
                            </tr>
                            <tr>
                                <td height="20" style="height:20px; font-size:1px; line-height:1px;">&nbsp;</td>
                            </tr>

                            <tr>
                                <td height="1" bgcolor="#efefef" style="height:1px; background-color:#efefef; font-size:0px; line-height:0px;">
                                    <img src="https://app.mailgun.com/assets/pilot/images/templates/spacer.gif" width="1" height="1" border="0" style="display:block;" />
                                </td>
                            </tr>

                            <tr>
                                <td height="25" class="em_h20" style="height:25px; font-size:1px; line-height:1px;">&nbsp;</td>
                            </tr>

                            <tr>
                                <td valign="top" align="center">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td valign="top">
                                                <table width="120" border="0" cellspacing="0" cellpadding="0" align="left" style="width:120px;" class="em_wrapper">
                                                    <tr>
                                                        <td valign="top" align="center">
                                                            <img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/Icon%20Only.png" width="90" height="120" alt="Logo" border="0" style="display:block; max-width:120px; font-family:Arial, sans-serif; font-size:17px; line-height:20px; color:#000000; font-weight:bold;" />
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table width="25" border="0" cellspacing="0" cellpadding="0" align="left" style="width:25px;" class="em_hide">
                                                    <tr>
                                                        <td valign="top" align="left" width="25" style="width:25px;" class="em_hide">&nbsp;</td>
                                                    </tr>
                                                </table>
                                                <table width="405" border="0" cellspacing="0" cellpadding="0" align="left" style="width:405px;" class="em_wrapper">
                                                    <tr>
                                                        <td height="16" style="height:16px; font-size:1px; line-height:1px;">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="em_grey" align="left" valign="top" style="font-family: Arial, sans-serif; font-size: 18px; line-height: 22px; color:#434343; font-weight:bold;">
                                                            {{$invoice->lines->data[0]->description}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="13" style="height:13px; font-size:1px; line-height:1px;">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="em_grey" align="left" valign="top" style="font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; color:#434343;">
                                                            Amount ($): <span style="color:#da885b; font-weight:bold;">
                                                                {{number_format($invoice->lines->data[0]->amount  / 100, 2)}}
                                                            </span>
                                                        </td>
                                                    </tr>

                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="25" class="em_h20" style="height:25px; font-size:1px; line-height:1px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="1" bgcolor="#efefef" style="height:1px; background-color:#efefef; font-size:0px; line-height:0px;">
                                    <img src="https://app.mailgun.com/assets/pilot/images/templates/spacer.gif" width="1" height="1" alt="" border="0" style="display:block;" />
                                </td>
                            </tr>
                            <tr>
                                <td height="21" class="em_h20" style="height:21px; font-size:1px; line-height:1px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td valign="top" align="right">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="right">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="em_grey" width="100" align="right" valign="top" style="font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; color:#434343; width:100px; font-weight:bold;">Total</td>
                                            <td width="20" style="width:20px; font-size:0px; line-height:0px;"></td>
                                            <td width="100" class="em_grey" align="right" valign="top" style="font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; color:#434343; width:100px; font-weight:bold;">
                                                ${{number_format($invoice->total / 100, 2)}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="36" style="height:36px;" class="em_h10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="em_full_wrap" align="center" bgcolor="#efefef">
    <tr>
        <td align="center" valign="top">
            <table align="center" width="650" border="0" cellspacing="0" cellpadding="0" class="em_main_table" style="width:650px; table-layout:fixed;">
                <tr>
                    <td align="center" valign="top" style="padding:0 25px;" class="em_aside10">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td height="40" style="height:40px;" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td width="30" style="width:30px;" align="center" valign="middle">
                                                <a href="https://www.facebook.com/flowrolled" target="_blank" style="text-decoration:none;">
                                                    <img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/fb.png" width="30" height="30" alt="Fb" border="0" style="display:block; font-family:Arial, sans-serif; font-size:18px; line-height:25px; text-align:center; color:#000000; font-weight:bold; max-width:30px;" />
                                                </a>
                                            </td>
                                            <td width="12" style="width:12px;">&nbsp;</td>
                                            <td width="30" style="width:30px;" align="center" valign="middle">
                                                <a href="https://twitter.com/flowrolled" target="_blank" style="text-decoration:none;">
                                                    <img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/tw.png" width="30" height="30" alt="Tw" border="0" style="display:block; font-family:Arial, sans-serif; font-size:14px; line-height:25px; text-align:center; color:#000000; font-weight:bold; max-width:30px;" />
                                                </a>
                                            </td>
                                            <td width="12" style="width:12px;">&nbsp;</td>
                                            <td width="30" style="width:30px;" align="center" valign="middle">
                                                <a href="https://www.instagram.com/flowr0lled" target="_blank" style="text-decoration:none;">
                                                    <img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/insta.png" width="30" height="30" alt="Insta" border="0" style="display:block; font-family:Arial, sans-serif; font-size:14px; line-height:25px; text-align:center; color:#000000; font-weight:bold; max-width:30px;" />
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="16" style="height:16px; font-size:1px; line-height:1px; height:16px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 15px; line-height: 18px; color:#434343; font-weight:bold;">Problems or questions?</td>
                            </tr>
                            <tr>
                                <td height="10" style="height:10px; font-size:1px; line-height:1px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" valign="top" style="font-size:0px; line-height:0px;">
                                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td width="15" align="left" valign="middle" style="font-size:0px; line-height:0px; width:15px;">
                                                <a href="mailto:meow@meowgun.com" style="text-decoration:none;">
                                                    <img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/email_img.png" width="15" height="12" alt="" border="0" style="display:block; max-width:15px;" />
                                                </a>
                                            </td>
                                            <td width="9" style="width:9px; font-size:0px; line-height:0px;" class="em_w5">
                                                <img src="https://app.mailgun.com/assets/pilot/images/templates/spacer.gif" width="1" height="1" alt="" border="0" style="display:block;" />
                                            </td>
                                            <td class="em_grey em_font_11" align="left" valign="middle" style="font-family: Arial, sans-serif; font-size: 13px; line-height: 15px; color:#434343;">
                                                <a href="mailto:john@flowrolled.com" style="text-decoration:none; color:#434343;">john@flowrolled.com</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="9" style="font-size:0px; line-height:0px; height:9px;" class="em_h10">
                                    <img src="https://app.mailgun.com/assets/pilot/images/templates/spacer.gif" width="1" height="1" alt="" border="0" style="display:block;" />
                                </td>
                            </tr>
                            <tr>
                                <td height="35" style="height:35px;" class="em_h20">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="1" bgcolor="#dadada" style="font-size:0px; line-height:0px; height:1px;">
                        <img src="https://app.mailgun.com/assets/pilot/images/templates/spacer.gif" width="1" height="1" alt="" border="0" style="display:block;" />
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
