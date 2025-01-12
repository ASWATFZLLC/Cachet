<template>
    <div>
        <div class="row">
            <div class="col-xs-10">
                <strong>
                    {{metric.name}}

                    <i class="ion ion-ios-help-outline" data-toggle="tooltip" :data-title="metric.description" v-if="metric.description"></i>
                </strong>
            </div>
            <div class="col-xs-2">
                <div class="dropdown pull-right">
                    <a href='javascript: void(0)' class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class='filter'>{{view.title || metric.default_view_name}}</span> <span class="caret"></span></a>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12" style="margin-bottom:20px">
                <canvas :id="metricId" height="180" width="600"></canvas>
            </div>

        </div>
    </div>
</template>

<script>
const Chart = require('chart.js')
const _ = require('lodash')

// Configure Chart.js
Chart.defaults.global.elements.point.hitRadius = 10
Chart.defaults.global.responsiveAnimationDuration = 1000
Chart.defaults.global.legend.display = false
function commarize(value) {
  min = 1e3;
  // Alter numbers larger than 1k
  if (value >= min) {
    var units = ["k", "M", "B", "T"];

    var order = Math.floor(Math.log(value) / Math.log(1000));

    var unitname = units[(order - 1)];
    var num = value / 1000 ** order; 
    num = num.toFixed(2)   
    return num + unitname
  }

  // return formatted original number
  return value.toLocaleString()
}

module.exports = {
    props: [
        'metric',
        'theme',
        'theme-light',
        'theme-dark'
    ],
    data () {
        return {
            canvas: null,
            context: null,
            chart: null,
            data: null,
            view: {
                param: null,
                title: null,
            },
            loading: false,
        }
    },
    mounted () {
        this.canvas = document.getElementById(this.metricId)
        this.context = this.canvas.getContext('2d')

        this.getData()

        $('.dropdown-toggle').dropdown()
    },
    computed: {
        metricId () {
            return `metric-${this.metric.id}`
        }
    },
    watch: {
        loading (val) {
            this.context.clearRect(0, 0, this.canvas.width, this.canvas.height)

            this.context.fillStyle = "#666"
            this.context.font = '44px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"'

            const textString = "Loading data",
                textWidth = this.context.measureText(textString).width

            this.canvas.textBaseline = 'middle'
            this.canvas.textAlign = "center"

            this.context.fillText(textString , (this.canvas.width / 2) - (textWidth / 2), 100)
        }
    },
    methods: {
        getData () {
            this.loading = true

            return axios.get('/metrics/'+this.metric.id, {
                params: {
                    filter: this.view.param
                }
            }).then(response => {
                this.data = response.data.data.items
                this.loading = false

                this.updateChart()
            })
        },
        changeView (param, title) {
            // Don't reload the same view.
            if (this.view.param === param) return

            this.view = {
                param: param,
                title: title
            }

            this.getData().then(this.updateChart)
        },
        updateChart () {
            if (this.chart !== null) {
                this.chart.destroy()
            }
            //Used in tooltip callback where this.metric is not the same.
            var metric = this.metric;
            /*
             * Datetimes are used as keys instead of just time in order to
             * improve ordering of labels in "Last 12 hours", so we cut the
             * labels.
             * This cutting is done only if there is an hour in the string, so
             * if the view by day is set it doesn't fail.
             */
            var data_keys = _.keys(this.data);
            if (0 < data_keys.length && data_keys[0].length > 10) {
                for (var i = 0; i < data_keys.length; i++) {
                    data_keys[i] = data_keys[i].substr(11);
                }
            }
            data_keys.splice(data_keys.length-1, 1);
            values = _.values(this.data)
            values.splice(values.length-1, 1);

            this.chart = new Chart(this.context, {
                type: 'line',
                data: {
                    labels: data_keys,
                    datasets: [{
                        data: values,
                        backgroundColor: this.themeLight,
                        borderColor: this.theme,
                        pointBackgroundColor: this.theme,
                        pointBorderColor: this.theme,
                        pointHoverBackgroundColor: this.themeDark,
                        pointHoverBorderColor: this.themeDark
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false,
                                suggestedMax: 0.1,
                                // fixedStepSize: result.data.metric.places,
                                callback: function(tickValue, index, ticks) {
                                    // Alter numbers larger than 1k
                                    if (tickValue >= 1e3) {
                                        return commarize(tickValue);
                                    }

                                    let delta = ticks[1] - ticks[0]

                                    // If we have a number like 2.5 as the delta, figure out how many decimal places we need
                                    if (Math.abs(delta) > 1) {
                                        if (tickValue !== Math.floor(tickValue)) {
                                            delta = tickValue - Math.floor(tickValue)
                                        }
                                    }

                                    const logDelta = Chart.helpers.log10(Math.abs(delta))
                                    let tickString = ''

                                    if (tickValue !== 0) {
                                        let numDecimal = -1 * Math.floor(logDelta)
                                        numDecimal = Math.max(Math.min(numDecimal, 2), 0) // Use as many places as the metric defines
                                        tickString = tickValue.toFixed(numDecimal)
                                    } else {
                                        tickString = '0' // Never show decimal places for 0
                                    }

                                    return tickString

                                   

                                }
                            }
                        },]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                value = tooltipItem.yLabel
                                if (tooltipItem.yLabel >= 1e3) {
                                    value=commarize(tooltipItem.yLabel);
                                }
                                return value + ' ' + metric.suffix;
                            }
                        }
                    }
            }})
        }
    }
}
</script>
