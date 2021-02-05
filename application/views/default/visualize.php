<section id="container-data" class="page-container">
    <!-- <aside id="as-help-data" class="as-help">
        <h3>Help</h3>
        <p>You can upload your own dataset for visualizing or search through our library of existing datasets.</p>
        <p>For data uploads, please ensure the file is of .csv format or it will not be processed correctly.</p> 
    </aside> -->

    <!-- <section id="data-upload" class="section-container data">
        <h1>Upload Data</h1>
        <form id="form-upload" enctype="multipart/form-data">
            <input type="file" name="file-to-upload" id="file-to-upload">
            <button type="submit" id="btn-upload-data" name="btn-upload-data" disabled>Visualize</button>
        </form>
    </section> -->

    <section id="data-search" class="section-container data">
        <h1>Search Data</h1>
        <div id="data-search-box">
            <input type="text" id="keywords" placeholder="search..">
            <button id="btn-search-data" name="btn-search-data"><i id="search-icon" class="material-icons">search</i></button>
        </div>

        <table id="tbl-search-results">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>   
    </section>

    <!-- <aside id="as-help-vis" class="as-help">
        <h3>Help</h3>
        <p>Each chart type displays different data types on its axes.</p>
        <ul>
            <li><p>A <b>bar chart</b> displays categories on its x-axis and numeric values on its y-axis.</p></li>
            <li><p>A <b>scatter plot</b> displays numeric values on both its axes.</p></li>          
            <li><p>A <b>line graph</b> displays time-series data, such as dates, on its x-axis and numeric values on its y-axis.</p></li>  
        </ul>
        <p>Once the chart type has been selected, axes selection boxes will be populated with the relevant data fields from the chosen dataset.
            If one of the axis boxes has no available selections, your datatset did not have a field of that data type.</p>
        <p>After selecting data for your axes, you will be able to generate the chart.</p>
        </p>The chart can be saved to your computer by clicking on the <b>Export as PNG</b> button.</p>
    </aside> -->

    <section id="data-options" class="section-container data">
        <section id="vis-data-file" class="vis">
            <h2>1. Select a data file.</h2>
            <select id="select-file-name" class="vis-select" disabled>
                <option value="" selected>select data file</option>
            </select>
        </section>

        <section id="vis-chart-type" class="vis">
            <h2>2. Select a chart type.</h2>
            <select id="chart-type" class="vis-select" disabled>
                <option value="" selected>select chart type</option>
            </select>
        </section>

        <section id="vis-axis-data" class="vis">
            <h2>3. Select the data for your x and y axis.</h2>
            <select id="x-axis-title" class="vis-select" disabled>
                <option value="" selected>select x-axis</option>
            </select>

            <select id="y-axis-title" class="vis-select" disabled>
                <option value="" selected>select y-axis</option>
            </select>
        </section>

        <section id="vis-generate" class=" vis">
            <h2>4. Generate the visualization.</h2>
            <button id="btn-generate" name="btn-generate" disabled>Generate</button>
        </section>
    </section>

    <section id="vis-chart" class="section-container vis">
        <svg id="chart"></svg>
        <button id="btn-export-png" hidden>Export as PNG</button>
    </section>
</section>


