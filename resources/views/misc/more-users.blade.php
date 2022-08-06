<a tabindex="0" data-trigger="focus" data-toggle="popover" data-html="true" data-placement="top" data-offset="0 4"
    data-content="
                         <div class='title'>{{ $more_users_title ?? __('lang.users')}}</div>
                         <div class='text-center'>

                         @foreach($users as $user)
                         <!--each user-->
                         <img src='{{ $user->avatar }}' data-toggle='tooltip' title='{{ $user->first_name }}'
                             data-placement='top' alt='{{ $user->first_name }}' class='img-circle avatar-xsmall'>
                         <!--each user-->   
                         @endforeach
                         </div>">
    <span class="btn btn-secondary btn-circle btn-sm more-users">
        <i class="ti-more"></i>
    </span>
</a>