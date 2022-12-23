
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{env('APP_NAME')}}</title>
    </head>
    <body>
        <table style="width:100%; margin:0 auto; text-align:left; border:1px solid #d6d6d6;font-size: 15px;border-top: none;font-family: arial, sans-serif; background: #ffe9e9" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table style="background: #ffe9e9; color:#000;width:100%; border-top: 4px solid #17a2b8;" cellspacing="0" cellpadding="0">
                        <tr>
                            <th style="padding: 5px; font-size: 30px;line-height: 21px; text-align:center;"><img src="{{asset('assets/images/logo.png')}}" style="width:100px"></th>
                            
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding:20px;">
                   <p>Dear {{$userName}},</p>
                   <br>
                   <p>
                    Welcome to {{env('APP_NAME')}}<br/>
                    Your new password is <b>{{$password}}</b>.<br/>We advise you to change the password after Login.<br>
                   </p>
				    <br><br>
				    <p>Thank you</p>
                    <p>Team {{env('APP_NAME')}}</p>
                </td>
            </tr>
            <tr>
                <td style="background: #f4f4f4; color:#000; padding:13px; font-size:16px;" align="center">{{ now()->format('Y') }} @ {{env('APP_NAME')}}. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>