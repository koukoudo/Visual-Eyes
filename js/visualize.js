$(document).ready(function() {

    var title;

    $('#btn-search-data').click(function() {
        var keywords = $('#keywords').val();
        $.ajax({
            type: 'POST',
            url: BASE_URL + "/Visualize/getDataSets",
            dataType: 'json',
            data: {keywords: keywords},
            error: function() {
                alert('No search results');
            },
            success: function(data) {  
                var html = '<tr>';
                for(var i in data) {
                    html += '<tr id = "row-'+data[i].dataID+'">';
                    html += '<td class="table_data" data-column_name="title">'+data[i].dataTitle+'</td>';
                    html += '<td class="table_data" data-column_name="description">'+data[i].description+'</td>';
                    html += '<td><button class="btn-select" name="btn-select" id="'+data[i].dataID+'"><span></span>Visualize</button></td></tr>';
                }
                $('tbody').html(html);                
            }
        })
    });

    $(document).on('click', '.btn-select', function() {
        $('#select-file-name').prop('disabled', true);
        $('#select-file-name').children('option:not(:first)').remove();

        $('#chart-type').prop('disabled', true);
        $('#chart-type').children('option:not(:first)').remove();

        $('#x-axis-title').prop('disabled', true);
        $('#x-axis-title').children('option:not(:first)').remove();

        $('#y-axis-title').prop('disabled', true);
        $('#y-axis-title').children('option:not(:first)').remove();

        $('#btn-generate').prop('disabled', true);

        d3.select('svg').selectAll('*').remove();

        var id = $(this).attr('id');

        $.ajax({
            type: 'POST',
            url: BASE_URL + "/Visualize/getDataFiles",
            dataType: 'json',
            data: {id: id},
            error: function() {
                alert('There was a problem selecting that data set. Please try again.');
            },
            success: function(data) { 
                for (var i in data) {
                    $('#select-file-name').append($("<option></option>")
                        .attr("value", data[i])
                        .text(data[i])); 
                }

                $('#select-file-name').prop('disabled', false);
            }
        });
    });

    $('#select-file-name').change(function() {
        $('#chart-type').prop('disabled', true);
        $('#chart-type').children('option:not(:first)').remove();

        $('#x-axis-title').prop('disabled', true);
        $('#x-axis-title').children('option:not(:first)').remove();

        $('#y-axis-title').prop('disabled', true);
        $('#y-axis-title').children('option:not(:first)').remove();

        $('#btn-generate').prop('disabled', true);

        d3.select('svg').selectAll('*').remove();

        var file = $('#select-file-name').val();

        $.ajax({
            type: 'POST',
            url: BASE_URL + "/Visualize/getDataTypes",
            dataType: 'json',
            data: {file: file},
            error: function() {
                alert('There was a problem selecting that data file. Please try again.');
            },
            success: function(data) {  
                $('#chart-type').children('option:not(:first)').remove();

                for (var i in data) {
                    $('#chart-type').append($("<option></option>")
                    .attr("value", data[i])
                    .text(data[i])); 
                }

                $('#chart-type').prop('disabled', false);
            }
        });
    });

    $('#chart-type').change(function() {
        $('#x-axis-title').prop('disabled', true);
        $('#x-axis-title').children('option:not(:first)').remove();

        $('#y-axis-title').prop('disabled', true);
        $('#y-axis-title').children('option:not(:first)').remove();

        $('#btn-generate').prop('disabled', true);

        d3.select('svg').selectAll('*').remove();

        var type = $('#chart-type').val();

        $.ajax({
            type: 'POST',
            url: BASE_URL + "/Visualize/getAxes",
            dataType: 'json',
            data: {type: type},
            error: function() {
                alert('There was a problem processing the data fields for the axes. Please try again.');
            },
            success: function(data) {  
                $('#x-axis-title').children('option:not(:first)').remove();
                $('#y-axis-title').children('option:not(:first)').remove();

                if (type == "Bar Chart") {
                    for (var i in data.numbers) {
                        $('#y-axis-title').append($("<option></option>")
                            .attr("value", data.numbers[i])
                            .text(data.numbers[i])); 
                    }

                    for (var i in data.strings) {
                        $('#x-axis-title').append($("<option></option>")
                            .attr("value", data.strings[i])
                            .text(data.strings[i])); 
                    }
                } else if (type == "Scatter Plot") {
                    for (var i in data.numbers) {
                        $('#y-axis-title').append($("<option></option>")
                            .attr("value", data.numbers[i])
                            .text(data.numbers[i])); 

                        $('#x-axis-title').append($("<option></option>")
                            .attr("value", data.numbers[i])
                            .text(data.numbers[i])); 
                    }
                } else if (type == "Line Graph") {
                    for (var i in data.numbers) {
                        $('#y-axis-title').append($("<option></option>")
                            .attr("value", data.numbers[i])
                            .text(data.numbers[i])); 
                    }

                    for (var i in data.dates) {
                        $('#x-axis-title').append($("<option></option>")
                            .attr("value", data.dates[i])
                            .text(data.dates[i])); 
                    }
                } 
                
                $('#x-axis-title').prop('disabled', false);
                $('#y-axis-title').prop('disabled', false);
            }
        });
    });

    $('#x-axis-title').change(function() {
        $('#y-axis-title').prop('disabled', false);
    });

    $('#y-axis-title').change(function() {
        $('#btn-generate').prop('disabled', false);
    });

    $('#btn-generate').click(function() {
        d3.select("chart").remove();
        var x_axis = $('#x-axis-title').children('option:selected').val();
        var y_axis = $('#y-axis-title').children('option:selected').val();
        var type = $('#chart-type').val();
        title = "A "+type+" chart showing the relationship between "+x_axis+" and "+y_axis;
        
        $.ajax({
            type: 'POST',
            url: BASE_URL + "/Visualize/create",
            dataType: 'json',
            data: {x_axis_title: x_axis, y_axis_title: y_axis},
            error: function() {
                alert('There was a problem generating the visualization. Please try again.');
            },
            success: function(data) {   
                var padding = 50,
                    height = 500,
                    width = 900;

                //create an SVG element and append it to the DOM
                var svg = d3.select("#chart")
                    .append("g")

                //add white background to chart
                svg.append("rect")
                    .attr("width", "100%")
                    .attr("height", "100%")
                    .attr("fill", "white");
                
                //add chart title
                svg.append("text")
                    .attr("x", (width / 2))             
                    .attr("y", 0 + (padding / 2))
                    .attr("text-anchor", "middle")  
                    .style("font-size", "15px") 
                    .style("text-decoration", "underline")
                    .text(title);

                if (type == "Bar Chart") {
                    var c10 = d3.scale.category10(); 

                    data.forEach(function(d) {
                        d[y_axis] = +d[y_axis]
                    });

                    var group = d3.map(data, d => d[x_axis]).keys();

                    //create x axis
                    var x = d3.scale.ordinal()
                        .rangeRoundBands([padding, width-(padding/2)], 0.2)
                        .domain(group)
                    svg.append("g")
                        .attr("class", "xaxis axis")
                        .attr("transform", "translate(0," + (height - (padding*2)) + ")")
                        .call(d3.svg.axis().scale(x).orient("bottom"));

                    var min = d3.min(data, d => d[y_axis]);
                    var max = d3.max(data, d => d[y_axis]);

                    //create y axis
                    var y = d3.scale.linear()
                        .range([height - (padding*2), padding])
                        .domain([min, max]);
                    svg.append("g")
                        .attr("class", "yaxis axis")
                        .attr("transform", "translate(" + padding + ",0)")
                        .call(d3.svg.axis().scale(y).orient("left"));

                    //add bars
                    svg.selectAll("rect")
                    .data(data)
                    .enter()
                    .append("rect")
                        .attr("class","bar")
                        .attr("width", x.rangeBand())
                        .attr("fill", function(d,i) {
                            return c10(Math.random()*10*i);
                          }) 
                        .attr("y", function(d) { return y(d[y_axis]); })
                        .attr("x", function(d) { return x(d[x_axis]); })              
                        .attr("height", function(d) {
                            return height-(padding*2)-y(+d[y_axis]);
                        })
                        
                } else if (type == "Scatter Plot") {

                    data.forEach(function(d) {
                        d[x_axis] = +d[x_axis];
                        d[y_axis] = +d[y_axis];
                    });

                    //create x axis
                    var x = d3.scale.linear()
                        .range([padding, width-padding])
                        .domain(d3.extent(data, function(d) { return d[x_axis]; }))
                    svg.append("g")
                        .attr("class", "xaxis axis")
                        .attr("transform", "translate(0," + (height - (padding*2)) + ")")
                        .call(d3.svg.axis().scale(x).orient("bottom"));

                    //create y axis
                    var y = d3.scale.linear()
                        .range([height - (padding*2), padding])
                        .domain(d3.extent(data, function(d) { return d[y_axis]; }));
                    svg.append("g")
                        .attr("class", "yaxis axis")
                        .attr("transform", "translate("+padding+",0)") 
                        .call(d3.svg.axis().scale(y).orient("left"));

                    var dots =  svg.selectAll("dot")
                        .data(data)
                        .enter()
                        .append("circle")
                          .attr("cx", function (d) { return x(d[x_axis]); } )
                          .attr("cy", function (d) { return y(d[y_axis]); } )
                          .attr("r", 2)
                          .style("fill", "darkblue")

                } else if (type == "Line Graph") {
                    var parseDate = d3.time.format("%Y-%m-%d").parse;

                    data.forEach(function(d) {
                        d[x_axis] = parseDate(d[x_axis]);
                        d[y_axis] = +d[y_axis];
                    }); 

                    //create x axis
                    var x = d3.time.scale()
                        .range([padding, width-padding])
                        .domain(d3.extent(data, function(d) { return d[x_axis]; }));
                    svg.append("g")
                        .attr("class", "xaxis axis")
                        .attr("transform", "translate(0,"+(height - (padding*2))+")")
                        .call(d3.svg.axis().scale(x).orient("bottom"));

                    //create y axis
                    var y = d3.scale.linear()
                        .range([height - (padding*2), padding])
                        .domain([0, d3.max(data, function(d) { return d[y_axis]; })]);
                    svg.append("g")
                        .attr("class", "yaxis axis")
                        .attr("transform", "translate("+padding+",0)") 
                        .call(d3.svg.axis().scale(y).orient("left"));

                    //create a line generator
                    var valueline = d3.svg.line()
                        .x(function(d) { return x(d[x_axis]); })
                        .y(function(d) { return y(d[y_axis]); });

                    //append the svg path
                    svg.append("path")
                        .attr("fill", "none")
                        .attr("stroke", "darkblue")
                        .attr("stroke-width", 1.5)
                        .attr("d", valueline(data));
                }

                svg.selectAll(".xaxis text")  // select all the text elements for the xaxis
                .attr("transform", "translate(-12,40) rotate(-90)")

                //add label to y axis
                svg.append("text")
                    .attr("class", "y label")
                    .attr("transform", "translate("+ (padding/2) +","+(height/2)+")rotate(-90)")
                    .attr("text-anchor", "middle")
                    .style("font-size", "15px")
                    .text(y_axis);

                //add label to x axis
                svg.append("text")
                    .attr("class", "x label")
                    .attr("transform", "translate("+ (width/2) +","+(height-(padding/4))+")")
                    .attr("text-anchor", "middle")
                    .style("font-size", "15px")
                    .text(x_axis);

                var svg = document.getElementById('chart');

                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                ctx.canvas.width  = 900;
                ctx.canvas.height = 500;
                canvg(canvas, svg.outerHTML);
        
                canvas.toBlob(function(blob) {
                    var fd = new FormData();
                    fd.append('title', title);
                    fd.append('chart', blob, title+'.png');
                    $.ajax({
                        type: 'POST',
                        url: BASE_URL + "/Visualize/addChartToDB",
                        data: fd,
                        processData: false,
                        contentType: false
                    });
                },'image/png'); 

                $('#btn-export-png').show();
            }
        });
    });

    $('#btn-export-png').click(function() {
        var svg = document.getElementById('chart');

        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        ctx.canvas.width  = 900;
        ctx.canvas.height = 500;
        canvg(canvas, svg.outerHTML);

        var fileURL = canvas.toDataURL("image/png");
        var link = document.createElement('a');
        link.download = title+".png";
        link.href = fileURL;
        link.click(); 
    })

    // $('#file-to-upload').change(() => {
    //     $('#form-upload').submit();
    // })

    // $('#form-upload').submit(function(e) {
    //     e.preventDefault();
    //     $.ajax({
    //         type: 'POST',
    //         url: BASE_URL + "/Visualize/uploadData",
    //         data: new FormData(this),
    //         processData:false,
    //         contentType:false,
    //         cache:false,
    //         error: function() {
    //             $('#file-to-upload').after('<small class="update-msg error">Unable to upload selected file. Please try again.</small>');
    //         },
    //         success: function(data) {  
    //             $('#file-to-upload').after('<small class="update-msg success">Successfully uploaded ' + data + '</small>');
    //             $('#btn-upload-data').prop('disabled', false);
    //         }
    //     })
    // });

    // $('#btn-upload-data').click(() => {
    //     $(location).attr('href', BASE_URL + "/Visualize/visualizeView"); 
    // })
});


