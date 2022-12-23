@extends('agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <style>
        .cursor-pointer{
            cursor: pointer;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <section id="justified-bottom-border">

                <div class="row match-height">

                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="card-title card-title-heading font-family-class">
                                            All XML Urls
                                        </h4>
                                    </div>

                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    @if (Session::has('error-sitemap'))
                                        <div class="alert alert-danger">
                                            {{ Session::get('error-sitemap') }}
                                        </div>
                                    @endif
                                    @if (Session::has('success-sitemap'))
                                        <div class="alert alert-success">
                                            {{ Session::get('success-sitemap') }}
                                        </div>
                                    @endif
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="datatableses">
                                            <thead>
                                            <tr>
                                                <th colspan="">Id</th>
                                                <th colspan="">Name</th>
                                                <th colspan="">Urls</th>
                                                <th colspan="" >Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i=1;?>
                                            @if($sitemaplinks)
                                                @foreach($sitemaplinks as $k)
                                                    <tr>
                                                        <td style="width:50px;">{{$i}}</td>
                                                        <td class="hidden-xs">{{$k["name"]}}</td>
                                                        <td class="max-texts border ">{{$k["links"]}}</td>
                                                        <td><a href="{{$k["generateUrl"]}}" class="btn btn-success" >Generate XML</a></td>
                                                    </tr>
                                                    <?php $i++;?>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div> <!-- container -->

    </div> <!-- content -->
    </div>
    </div>
@endsection
@section('pageLevelJS')
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <!-- Toastr js -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>
    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
@endsection
