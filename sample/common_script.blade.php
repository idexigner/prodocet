<script>

$(document).ajaxError(function (event, jqxhr, settings, exception) {
    if (jqxhr.status == 401 || jqxhr.status == 500) {
        
        location.reload()
    }
   
});

var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// Custom toast function
function _toast(type, message, timer = 4000) {
    Toast.fire({
        icon: type,
        title: message,
        timer: timer
    });
}

// Custom error handler
function _handleError(xhr) {
    console.log(xhr);
    if (xhr.status === 422) {
        // Handle validation errors
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            // Get all error messages regardless of field
            var allErrors = Object.values(xhr.responseJSON.errors).flat();
            var errorMessage = allErrors.join('<br>');
            _toast('error', errorMessage);
        } else {
            _toast('error', xhr.responseJSON?.message || 'Validation failed');
        }
    } else if (xhr.responseJSON && xhr.responseJSON.message) {
        _toast('error', xhr.responseJSON.message);
    } else {
        _toast('error', 'Something went wrong');
    }
}

// Custom success handler
function _handleSuccess(message) {
    _toast('success', message);
}

// Custom AJAX request handler
function _ajaxRequest(url, type, data, successCallback, errorCallback) {
    $.ajax({
        url: url,
        type: type,
        data: data,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (successCallback) {
                successCallback(response);
            }
        },
        error: function(xhr) {
            console.log("chr", xhr)
            if (errorCallback) {
                errorCallback(xhr);
            } else {
                console.log("else")
                _handleError(xhr);
            }
        }
    });
}
function _ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

 // Function to check for duplicate item+subitem combination
 function isDuplicateItemSubitem(itemId, subitemId, currentRow) {
                var isDuplicate = false;
                $('.entry-row').each(function() {
                    var row = $(this);
                    if (row[0] !== currentRow[0]) { // Skip current row
                        var rowItemId = row.find('select[name*="item_id"]').val();
                        var rowSubitemId = row.find('select[name*="subitem_id"]').val();
                        if (rowItemId === itemId && rowSubitemId === subitemId) {
                            isDuplicate = true;
                            return false; // Break the loop
                        }
                    }
                });
                return isDuplicate;
            }
function declareSummernote(param){
// $(param).summernote({
//         height:100,
//         toolbar: [
//         // ['style', ['style']],
//         ['font', ['bold', 'italic', 'underline']],
//         // ['fontname', ['fontname']],
//         // ['color', ['color']],
//         ['para', ['ul', 'ol', 'paragraph']],
//         // ['height', ['height']],
//         // ['table', ['table']],
//         ['insert', ['link']],//, 'picture', 'hr'
//         ['view', ['fullscreen', 'codeview']],
//         // ['help', ['help']]
//         ],
//     });
}

function initializeDataTable(tableName, ajaxUrl, columnsArray, options = {}) {
    $.fn.dataTable.ext.errMode = 'none';
    
    var defaultOptions = {
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxUrl,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                // Add filter data if filter selectors are provided
                if (options.filterSelectors) {
                    options.filterSelectors.forEach(function(selector) {
                        var value = $(selector).val();
                        if (value !== undefined) {
                            d[$(selector).attr('name') || selector.replace('#', '')] = value;
                        }
                    });
                }
            }
        },
        columns: columnsArray,
        "order": [],
        "language": {
            "search": "Search Records:",
            "lengthMenu": "Show _MENU_ Records",
            "zeroRecords": "No matching records found",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "Showing 0 to 0 of 0 records",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "pageLength": 25
    };

    // Merge default options with provided options
    var finalOptions = { ...defaultOptions, ...options };
    
    return $(tableName).DataTable(finalOptions);
}

function formatDate(dateString) {
const parts = dateString.split('-');
const year = parts[0];
const month = parts[1];
const day = parts[2];
return `${month}/${day}/${year}`;
}
function formatDateWithMonth(dateString){
var date = new Date(dateString); // Create a new date object from data
  var day = date.getDate();
  var months = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
  var month = months[date.getMonth()];
  var year = date.getFullYear();

  // Construct a string in the format 'day-month-year'
  var formattedDate = day + '-' + month + '-' + year;

  return formattedDate;
}

function multipleUploadShow(inputId, inputFiles){
      

      // Get the file input element
      const fileInput = documeneInput = document.getElementById(inputId);
      // Add an event listener for file selection
      fileInput.addEventListener('change', function(event) {
          // Get the selected files
          const files = event.target.files;

          // Get the fileNames div element
          const fileNamesDiv = document.getElementById(inputFiles);
          // Clear any existing filenames
          fileNamesDiv.innerHTML = '';
          
          for (let i = 0; i < files.length; i++) {               
              const fileNamePara = document.createElement('li');                
              fileNamePara.textContent = files[i].name;                
              fileNamesDiv.appendChild(fileNamePara);
          }
      });



}

// Custom confirmation dialog
function _confirmDialog(options = {}) {
    const defaultOptions = {
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true,
        showCancelButton: true
    };

    return Swal.fire({
        ...defaultOptions,
        ...options
    });
}

// Custom delete confirmation with callback
function _confirmDelete(options = {}) {
    const defaultOptions = {
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true,
        showCancelButton: true,
        deleteUrl: '',
        deleteData: {},
        successCallback: null,
        errorCallback: null,
        table: null,
        currentPage: null
    };

    const settings = { ...defaultOptions, ...options };

    return _confirmDialog({
        title: settings.title,
        text: settings.text,
        icon: settings.icon,
        confirmButtonText: settings.confirmButtonText,
        cancelButtonText: settings.cancelButtonText,
        reverseButtons: settings.reverseButtons,
        showCancelButton: settings.showCancelButton
    }).then((result) => {
        if (result.isConfirmed) {
            _ajaxRequest(
                settings.deleteUrl,
                'DELETE',
                settings.deleteData,
                function(response) {
                    if (settings.successCallback) {
                        settings.successCallback(response);
                    } else {
                        _handleSuccess("Record deleted successfully");
                    }
                    
                    if (settings.table) {
                        settings.table.ajax.reload();
                        if (settings.currentPage !== null) {
                            settings.table.page(settings.currentPage).draw('page');
                        }
                    }
                },
                settings.errorCallback
            );
        }
    });
}

// Function to format number with commas
function formatNumber(number) {
    // Return null if null is given
    if (number === null) {
        return null;
    }
    // First remove any existing commas
    number = number.toString().replace(/,/g, '');
    // Convert to number to ensure we have a valid number
    number = parseFloat(number) || 0;
    // Format with commas
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

</script>