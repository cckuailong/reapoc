'use strict';
(function($){

    $(document).ready( function(){
        var ctxChart = document.getElementById( 'sgReportChart' );
        if ( ctxChart ) {
            var ctx = ctxChart.getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: typeof window.sgReports.type !== 'undefined' ? window.sgReports.type : 'bar',

                // The data for our dataset
                data: {
                    labels: window.sgReports.labels,
                    datasets: window.sgReports.datasets
                },

                // Configuration options go here
                options: {
                    aspectRatio: 3,
                    title: {
                        display: false
                    },
                    legend: {
                        display: typeof window.sgReports.legend !== 'undefined' ? window.sgReports.legend : false,
                        position: 'bottom'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }]
                    }
                }
            });
        }

        $(document).find('.sg-select2:not(.enhanced)').each(function(){
           $(this).select2({
               data: [
                   {
                       "id": 1,
                       "text": "Option 1"
                   },
                   {
                       "id": 2,
                       "text": "Option 2"
                   }
               ]
           });
        });


        $( document ).on( 'click', '.sg-report-options-switch', function(e){
            $(this).toggleClass('on');
            $('.sg-report-view').toggleClass('with-options')
            $('.sg-report-options').toggleClass('hidden');
        });
    });


})(jQuery);





