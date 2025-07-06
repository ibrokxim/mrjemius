@extends('layouts.app')

@section('title', __('terms_title'))

@section('content')
    <main>
        <section class="mt-8 mb-14">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 mx-auto">
                        <div class="card p-4 p-md-5">
                            <div class="card-body">
                                <h1 class="mb-4">{{ __('terms_section_1_header') }}</h1>

                                <p>{{ __('terms_section_1_p1') }}</p>
                                <p>{{ __('terms_section_1_p2') }}</p>
                                <p>{{ __('terms_section_1_p3') }}</p>
                                <ul>
                                    <li>{{ __('terms_section_1_li1') }}</li>
                                    <li>{{ __('terms_section_1_li2') }}</li>
                                    <li>{{ __('terms_section_1_li3') }}</li>
                                </ul>

                                <hr class="my-5">

                                <p>{{ __('terms_section_2_p1') }}</p>
                                <h4 class="mt-4">{{ __('terms_section_2_p2') }}</h4>

                                <h5 class="mt-4"><strong>{{ __('terms_subsection_rights_header') }}</strong></h5>
                                <p>{{ __('terms_subsection_rights_p1') }}</p>

                                <h5 class="mt-4"><strong>{{ __('terms_subsection_sellers_header') }}</strong></h5>
                                <p>{{ __('terms_subsection_sellers_p1') }}</p>

                                <h5 class="mt-4"><strong>{{ __('terms_subsection_return_header') }}</strong></h5>
                                <p>{{ __('terms_subsection_return_p1') }}</p>

                                <h5 class="mt-4"><strong>{{ __('terms_subsection_responsibility_header') }}</strong></h5>
                                <p>{{ __('terms_subsection_responsibility_p1') }}</p>

                                <h5 class="mt-4"><strong>{{ __('terms_subsection_protection_header') }}</strong></h5>
                                <p>{{ __('terms_subsection_protection_p1') }}</p>

                                <h5 class="mt-4"><strong>{{ __('terms_subsection_ecommerce_header') }}</strong></h5>
                                <p>{{ __('terms_subsection_ecommerce_p1') }}</p>

                                <h5 class="mt-4"><strong>{{ __('terms_subsection_literacy_header') }}</strong></h5>
                                <p>{{ __('terms_subsection_literacy_p1') }}</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
