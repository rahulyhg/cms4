<div id="{{ $chart->getElementId() }}"></div>

@push('footer')
    <script type="text/javascript">
        jQuery(function () {
            `use strict`;

            Morris.{{ $chart->__chart_type }}(
                    {!! $chart->toJSON() !!}
            );
        });
    </script>
@endpush