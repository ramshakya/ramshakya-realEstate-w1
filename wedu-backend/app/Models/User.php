<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'first_name',
        'last_name',
        'phone_number',
        'profile_image_id',
        'domain',
        'alt_address',
        'alt_phone',
        'alt_email',
        'identification_document_type',
        'identification_document_number',
        'identification_document_issue_authority',
        'age',
        'date_of_birth',
        'social_mobile',
        'fb_id',
        'fb_friends_list',
        'is_email_verified',
        'gender_id',
        'country_id',
        'person_id',
        'status_id',
        'AdminId',
        'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function get_userinfo($request){
        $data=User::where('email', $request->Email)
            ->first();

        return $data;
    }
}
