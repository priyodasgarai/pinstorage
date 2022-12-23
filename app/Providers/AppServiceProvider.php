<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('jwt', function (Request $request) {
            header('Content-type: application/json');
            if(is_null($request->bearerToken())):
                $response = [
                    'status' => FALSE,
                    'message' => 'Authorization Token Not Present In The Request !',
                    'data'      => (object)[]
                   ];
                http_response_code(401);
                print json_encode($response);
                exit;
            endif;
            try{
                $tokenPayload = JWT::decode($request->bearerToken(), new Key(config('jwt.key'), 'HS256'));
                $userDetails = \App\Models\User::where('id',$tokenPayload->id)->where('app_access_token',$request->bearerToken())->first();
                if(!is_null($userDetails)):
                    if($userDetails->status ==3):
                        $response =  (object)[
                            'status'    => FALSE,
                            'message'   => 'Account Has Been Deleted!',
                            'data'      => (object)[]
                        ];
                        http_response_code(404);
                        print json_encode($response);
                        exit;
                    elseif($userDetails->status == 0):
                        $response =  (object)[
                            'status'    => FALSE,
                            'message'   => 'Account Deactivated!',
                            'data'      => (object)[]
                        ];
                        http_response_code(400);
                        print json_encode($response);
                        exit;
                    endif;
                    if ($tokenPayload->expireTime > time()) :
                        return $tokenPayload;
                    else:
                        $response =  (object)[
                            'status' => FALSE,
                            'message' => 'Token Has Been Expired!',
                            'data'      => (object)[]
                        ];
                        http_response_code(440);
                        print json_encode($response);
                        exit;
                    endif;
                else:
                    $response =  (object)[
                        'status' => FALSE,
                        'message' => 'Invalid Authorization Token!',
                        'data'      => (object)[]
                    ];
                    http_response_code(401);
                    print json_encode($response);
                    exit;
                endif;
            } catch(\Exception $e){
                // print $e->getMessage(); die;
                $response =  [
                    'status' => FALSE,
                    'message' => $e->getMessage(),
                    'data'      => (object)[]
                ];
                http_response_code(401);
                print json_encode($response);
                exit;
            }
        });
    }
}
