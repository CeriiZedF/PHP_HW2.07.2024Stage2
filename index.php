<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Table with Pagination</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            margin: 20px;
        }
        #sidebar {
            width: 20%;
            padding-right: 20px;
            display: flex;
            flex-direction: column;
        }
        #sidebar h4 {
            margin-bottom: 20px;
        }
        #main-content {
            width: 80%;
        }
        .pagination-controls {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .pagination-controls input {
            width: 50px;
            text-align: center;
            margin: 0 10px;
        }
        .filter-section {
            margin-bottom: 10px;
        }
        .sort-button {
            cursor: pointer;
            margin-right: 10px;
        }
        .filter-checkbox {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div id="sidebar">
        <div class="pagination-controls">
            <button id="previousPage" class="btn btn-primary">Previous</button>
            <input type="number" id="pageNumber" min="1">
            <button id="nextPage" class="btn btn-primary">Next</button>
            <select id="recordsPerPage" class="form-select">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="100">100</option>
            </select>
        </div>
        <hr>
        <div id="sortControls">
            <div class="sort-section">
                <label>Sort by Name</label>
                <button class="sort-button" data-column="Name" data-order="asc">Ascending</button>
                <button class="sort-button" data-column="Name" data-order="desc">Descending</button>
            </div>
            <div class="sort-section">
                <label>Sort by Surname</label>
                <button class="sort-button" data-column="Surname" data-order="asc">Ascending</button>
                <button class="sort-button" data-column="Surname" data-order="desc">Descending</button>
            </div>
            <div class="sort-section">
                <label>Sort by Country</label>
                <button class="sort-button" data-column="Country" data-order="asc">Ascending</button>
                <button class="sort-button" data-column="Country" data-order="desc">Descending</button>
            </div>
            <div class="sort-section">
                <label>Sort by City</label>
                <button class="sort-button" data-column="City" data-order="asc">Ascending</button>
                <button class="sort-button" data-column="City" data-order="desc">Descending</button>
            </div>
            <div class="sort-section">
                <label>Sort by Salary</label>
                <button class="sort-button" data-column="Salary" data-order="asc">Ascending</button>
                <button class="sort-button" data-column="Salary" data-order="desc">Descending</button>
            </div>
        </div>
        <hr>
        <div id="countryFilters" class="filter-section">
            <label>Country Filters</label><br>
            <input type="text" id="countryFilterInput" class="form-control" placeholder="Search countries">
            <div id="countryFilterCheckboxes">
                <!-- Данні будуть завантажуватись за допомогою AJAX -->
            </div>
        </div>
        <hr>
        <div id="cityFilters" class="filter-section">
            <label>City Filters</label><br>
            <input type="text" id="cityFilterInput" class="form-control" placeholder="Search cities">
            <div id="cityFilterCheckboxes">
                <!-- Данні будуть завантажуватись за допомогою AJAX -->
            </div>
        </div>
    </div>
    <div id="main-content">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Salary</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
                <!-- Данні будуть завантажуватись за допомогою AJAX -->
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            var currentPage = 1;
            var recordsPerPage = $('#recordsPerPage').val();

            loadFilters();
            loadTable();

            $('.sort-button').click(function () {
                var column = $(this).data('column');
                var order = $(this).data('order');
                loadTable(column, order);
                $(this).data('order', order === 'asc' ? 'desc' : 'asc');
            });

            $('#recordsPerPage').change(function () {
                currentPage = 1;
                loadTable();
            });

            $('#nextPage').click(function () {
                currentPage++;
                loadTable();
            });

            $('#previousPage').click(function () {
                if (currentPage > 1) {
                    currentPage--;
                    loadTable();
                }
            });

            $('#pageNumber').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    var page = parseInt($(this).val());
                    if (page > 0) {
                        currentPage = page;
                        loadTable();
                    }
                }
            });

            $('#countryFilterInput').on('input', function () {
                var filter = $(this).val().toLowerCase();
                $('#countryFilterCheckboxes input').each(function () {
                    if ($(this).attr('id').toLowerCase().includes(filter)) {
                        $(this).parent().show();
                    } else {
                        $(this).parent().hide();
                    }
                });
            });

            $('#cityFilterInput').on('input', function () {
                var filter = $(this).val().toLowerCase();
                $('#cityFilterCheckboxes input').each(function () {
                    if ($(this).attr('id').toLowerCase().includes(filter)) {
                        $(this).parent().show();
                    } else {
                        $(this).parent().hide();
                    }
                });
            });

            $('#countryFilterCheckboxes, #cityFilterCheckboxes').on('change', 'input', function () {
                loadTable();
            });

            function loadFilters() {
                $.ajax({
                    url: 'get_filters.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var countries = data.countries;
                        var cities = data.cities;

                        $('#countryFilterCheckboxes').empty();
                        $('#cityFilterCheckboxes').empty();

                        countries.forEach(function (country) {
                            $('#countryFilterCheckboxes').append(
                                `<div><input type="checkbox" id="${country}" class="filter-checkbox"> ${country}</div>`
                            );
                        });

                        cities.forEach(function (city) {
                            $('#cityFilterCheckboxes').append(
                                `<div><input type="checkbox" id="${city}" class="filter-checkbox"> ${city}</div>`
                            );
                        });
                    }
                });
            }

            function loadTable(column = 'Name', order = 'ASC') {
                var countries = $('#countryFilterCheckboxes input:checked').map(function () {
                    return $(this).attr('id');
                }).get();
                var cities = $('#cityFilterCheckboxes input:checked').map(function () {
                    return $(this).attr('id');
                }).get();
                recordsPerPage = $('#recordsPerPage').val();

                $.ajax({
                    url: 'get_employees.php',
                    type: 'GET',
                    data: {
                        country: countries,
                        city: cities,
                        column: column,
                        order: order,
                        page: currentPage,
                        recordsPerPage: recordsPerPage
                    },
                    success: function (data) {
                        $('#employeeTableBody').html(data);
                        $('#pageNumber').val(currentPage);
                    }
                });
            }
        });
    </script>
</body>
</html>
