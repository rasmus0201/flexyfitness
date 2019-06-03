<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'last_login_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The api password to flexybox
     *
     * @var [type]
     */
    private $apiPassword;

    /**
     * A user can have many bookings
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany('App\Booking');
    }

    /**
     * A user can have many calendar weeks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function calendarWeeks()
    {
        return $this->hasMany('App\Calendar');
    }

    /**
     * Removes all data related to user
     * Remove the user afterwards
     *
     * @return void
     */
    public function nuke()
    {
        $this->bookings()->delete();
        $this->calendarWeeks()->delete();
        $this->delete();
    }

    /**
     * Get api password as attribute
     * Access with "->api_password"
     *
     * @return string
     */
    public function getApiPasswordAttribute()
    {
        return $this->apiPassword;
    }

    /**
     * Set the api password
     *
     * @param string $password
     *
     * @return this
     */
    public function setApiPasswordAttribute($password)
    {
        $this->apiPassword = $password;

        return $this;
    }

    /**
     * Get token-like api credentials
     *
     * @return string
     */
    public function getApiCredentials()
    {
        return base64_encode(json_encode([
            'username' => $this->username,
            'password' => $this->apiPassword
        ]));
    }

    /**
     * Auth a user by their credentials token string
     *
     * @param  string $token
     *
     * @return User
     */
    public static function authByToken($token)
    {
        $credentials = json_decode(base64_decode($token));

        if (!$user = User::where('username', $credentials->username)->first()) {
            return;
        }

        if (!Hash::check($credentials->password, $user->password)) {
            return;
        }

        return $user->setApiPasswordAttribute($credentials->password);
    }

    /**
     * Update a user by their credentials token string
     *
     * @param  string $token
     *
     * @return User
     */
    public static function updateByToken($token)
    {
        $credentials = json_decode(base64_decode($token));

        // Get or create a user
        $user = User::firstOrCreate(
            ['username' => $credentials->username],
            ['password' => Hash::make($credentials->password)]
        );

        // Check if passwords matches, if not
        // user changed password from Flexyfitness
        if (!Hash::check($credentials->password, $user->password)) {
            $user->password = Hash::make($credentials->password);
        }

        // Update last_login_at
        $user->last_login_at = Carbon::now();

        $user->save();

        return $user->setApiPasswordAttribute($credentials->password);
    }
}
