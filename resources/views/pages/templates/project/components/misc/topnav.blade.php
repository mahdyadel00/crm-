<div class="row">
    <div class="col-lg-12">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs profile-tab project-top-nav list-pages-crumbs" role="tablist">
            <!--details-->
            <li class="nav-item">
                <a class="nav-link tabs-menu-item   js-dynamic-url js-ajax-ux-request" data-toggle="tab"
                    id="tabs-menu-details" data-loading-class="loading-tabs"
                    data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/templates/projects') }}/{{ $project->project_id }}/details"
                    data-url="{{ _url('/templates/projects') }}/{{ $project->project_id }}/project-details"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.details')) }}</a>
            </li>
            <!--[tasks]-->
            <li class="nav-item">
                <a class="nav-link tabs-menu-item   js-dynamic-url js-ajax-ux-request" data-toggle="tab"
                    id="tabs-menu-tasks" data-loading-class="loading-tabs" data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/templates/projects') }}/{{ $project->project_id }}/tasks?filter_pt=template"
                    data-url="{{ url('/tasks') }}?source=ext&taskresource_type=project&taskresource_id={{ $project->project_id }}&filter_pt=template"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.tasks')) }}</a>
            </li>
            <!--[milestones]-->
            <li class="nav-item">
                <a class="nav-link  tabs-menu-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_milestones'] ?? '' }}"
                    data-toggle="tab" id="tabs-menu-milestones" data-loading-class="loading-tabs"
                    data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/templates/projects') }}/{{ $project->project_id }}/milestones"
                    data-url="{{ url('/milestones') }}?source=ext&milestoneresource_type=project&milestoneresource_id={{ $project->project_id }}"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.milestones')) }}</a>
            </li>

            <!--[files]-->
            <li class="nav-item">
                <a class="nav-link  tabs-menu-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_files'] ?? '' }}"
                    data-toggle="tab" id="tabs-menu-files" data-loading-class="loading-tabs"
                    data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/templates/projects') }}/{{ $project->project_id }}/files"
                    data-url="{{ url('/files') }}?source=ext&fileresource_type=project&fileresource_id={{ $project->project_id }}"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.files')) }}</a>
            </li>
            <!--[comments]-->
        </ul>
        <!-- Tab panes -->

        @include('pages.files.components.actions.checkbox-actions')

    </div>
</div>