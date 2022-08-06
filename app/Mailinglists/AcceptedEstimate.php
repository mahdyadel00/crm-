<?php
namespace App\Mailinglists;

use App\Models\User;


class AcceptedEstimate {

    /**
     * The leads repository instance.
     */
    protected $user;
    protected $notification_type;

    /**
     * Inject dependecies
     */
    public function __construct(User $users) {
        
        $this->user = $users;
    }

    /**
     * Which team members will receive emails for events on any estimate.
     * The following members will get emails:
     *   - have role permission : 'role_estimates' > 0
     *   - have notification preference : 'notifications_billing_activity' == 'yes_email'
     * @param string notification_type [email|notification]
     * @return object
     */
    public function build($notification_type='') {

        //start query
        $query = $this->user->newQuery();
        $query->where('type', '=', 'team');

        //with roles
        $query->with([
            'role',
        ]);

        //get the users
        $users = $query->get();

        //check every users permissions and email preference settings
        if ($notification_type == 'email') {
            foreach ($users as $key => $user) {
                if ($user->role->role_estimates == 0 || !in_array($user->notifications_billing_activity, ['yes_email'])) {
                    //drop user
                    $users->forget($key);
                }
            }
            //return user
            return $users;
        }

        //check every users permissions and email preference settings
        if ($notification_type == 'notification' || $notification_type == 'both') {
            foreach ($users as $key => $user) {
                if ($user->role->role_estimates == 0 || !in_array($user->notifications_billing_activity, ['yes', 'yes_email'])) {
                    //drop user
                    $users->forget($key);
                }
            }
            //return user
            return $users;
        }

        //no valid users
        return [];
    }

}