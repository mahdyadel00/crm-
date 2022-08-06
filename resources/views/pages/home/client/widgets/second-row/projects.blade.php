<div class="col-lg-6  col-md-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ cleanLang(__('lang.my_projects')) }}</h5>
            @php $projects = $payload['my_projects'] ; @endphp
            <div class="dashboard-projects" id="dashboard-client-projects">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>{{ cleanLang(__('lang.title')) }}</th>
                            <th>{{ cleanLang(__('lang.due_date')) }}</th>
                            <th>{{ cleanLang(__('lang.status')) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td class="text-center">{{ $project->project_id }}</td>
                            <td class="txt-oflo"><a
                                    href="{{ _url('projects/'.$project->project_id) }}">{{ str_limit($project->project_title ??'---', 20) }}</a>
                            </td>
                            <td>{{ runtimeDate($project->project_date_due) }}</td>
                            <td><span class="text-success"><span
                                        class="label {{ runtimeProjectStatusColors($project->project_status, 'label') }}">{{ runtimeLang($project->project_status) }}</span></span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>