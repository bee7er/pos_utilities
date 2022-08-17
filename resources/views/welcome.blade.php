@extends('template')

@section('content')

    <div id="wrapper">
        <div id="page" class="container">
            <div>
                <div class="title">POS</div>
                <p>
                    This web site is provided to assist with the identification of the page
                    numbers of products in the product price file, because the page number is needed
                    when printing specific POS tickets.
                </p>
                <p>
                    The admin area allows you to update to the latest price file. There is a bug in the PDF
                    parser library I am using and it runs out of memory with the actual price file, so take the default
                    which loads about a third of it.
                </p>
                <p>
                    Please note that this website is for demo purposes only.
                    <span style="color:#c40000;font-weight: bold;">The maximum product code available is {{ $maximumProductCode }}.</span>
                </p>
            </div>
        </div>
    </div>

@endsection