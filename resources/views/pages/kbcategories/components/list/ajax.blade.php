@if(isset($categories))
@foreach($categories as $category)
<!--each category-->
<div class="col-sm-12 col-md-4 col-lg-3" id="category_{{ $category->kbcategory_id ?? '' }}">
    <div class="card kb-category">
        <div class="card-body">
            <!--visibility-->
            @if(auth()->user()->role->role_knowledgebase > 1)
            <span class="kb-hover-icons hidden x-team label label-with-icon"><i class="sl-icon-eye"></i>
                {{ runtimeLang($category->kbcategory_visibility) }}</span>
            @endif
            <!--category icon-->
            <div class="kb-category-icon"><span><i class="{{ $category->kbcategory_icon ?? 'sl-icon-docs' }}"></i></span></div>
            <!--title-->
            <h5 class="card-title">{{ $category->kbcategory_title ?? '' }}</h5>
            <!--description-->
            <div class="card-text">{!! clean($category->kbcategory_description ?? '---') !!}</div>
            <a href="/kb/articles/{{ $category->kbcategory_slug }}" class="btn btn-sm btn-rounded-x btn-outline-info">{{ cleanLang(__('lang.see_articles')) }}</a>
        </div>
    </div>
</div>
<!--each category-->
@endforeach
@endif