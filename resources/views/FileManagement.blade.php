@extends('layouts')

@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
    <style>
        .dropdown-btn::after {
            display: none;
            width: 24px;
            height: 24px;
        }

        .selectableItemsContainer{
            cursor: pointer;
        }
    </style>
@endsection

@section('body')
    <div class="container mt-5">
        <h2>[Out Of Service]</h2>
        <!-- Breadcrumb -->
        <div class="row d-flex justify-content-between">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-flex align-items-center">
                        <li class="breadcrumb-item">
                            <a href="#" class="d-flex align-items-center">
                                <img src="{{asset('img/icons/FileManagement/building.png')}}" alt="Home Icon" height="30px" class="m-2 mt-0 mb-0">Root
                            </a>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col d-flex justify-content-end">
                <!-- Change Files Button -->
                <div class="text-right upload-btn">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveChangesModal">Change Files</button>
                </div>
            </div>
        </div>

        <!-- Folders Section -->
        <h5><b>Folders</b></h5>
        <div class="row row-cols-1 row-cols-lg-4 row-cols-lx-4 row-cols-md-2 row-cols-sm-2" id="FolderContainer">
            {{-- List of foders --}}
            <!-- New Folder Button Modal -->
            <div class="col folder mb-3">
                <div class="card shadow selectableItemsContainer" style="background-color: var(--primary-color-light);" id="createNewFolder">
                    <div class="row row-cols-2 d-flex align-items-center" style="height: 64px;">
                        <div class="col col-4 d-flex justify-content-end align-items-center" style="height: 100%;">
                            <img src="{{asset('img/icons/FileManagement/add.png')}}"
                                class="folder-icon p-2 pt-0 pb-0" alt="Folder Icon" height="40%">
                        </div>
                        <div class="col">
                            <span class="folder-name" style="color: #65BEFF;"><b>Create New Folder</b></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="mt-3">
        <!-- Files Section -->
        <h5 class="mt-3"><b>Files</b></h5>
        <div class="row row-cols-lg-4 row-cols-lx-4 row-cols-md-2 row-cols-sm-2" id="FileContainer">
            {{-- List of files --}}
            <!-- Upload New File Button Modal -->
            <div class="col mb-3" >
                <div class="card shadow selectableItemsContainer" style="background-color: var(--primary-color-light);" id="UploadNewFile">
                    <div class="row pt-3 row-cols-1 d-flex align-items-center" style="height: 180px;">
                        <div class="col d-flex justify-content-center align-items-center" style="height: 60%;">
                            <img src="{{asset('img/icons/FileManagement/add.png')}}"
                                class="folder-icon p-2" alt="Upload Icon" height="100%" width="auto">
                        </div>
                        <div class="col d-flex justify-content-center" style="height: 40%;">
                            <span class="folder-name" style="color: #65BEFF;"><b>Upload New File</b></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Modal for Uploading File -->
    <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadFileModalLabel">Upload New File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Choose File</label>
                        <input class="form-control" type="file" id="fileInput">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="uploadFileBtn">Upload File</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Creating New Folder -->
    <div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folderName" placeholder="Enter folder name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="createFolderBtn">Create Folder</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Updating File -->
    <div class="modal fade" id="updateFileModal" tabindex="-1" aria-labelledby="updateFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateFileModalLabel">Update File Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="fileIdForUpdate"> <!-- Hidden input for file ID -->
                        <div class="mb-3">
                            <label for="fileName" class="form-label">New File Name</label>
                            <input type="text" class="form-control" id="fileName" placeholder="Enter new file name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveFileChangesBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Updating Folder -->
    <div class="modal fade" id="updateFolderModal" tabindex="-1" aria-labelledby="updateFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateFolderModalLabel">Update Folder Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="folderIdForUpdate"> <!-- Hidden input for folder ID -->
                        <div class="mb-3">
                            <label for="folderName" class="form-label">New Folder Name</label>
                            <input type="text" class="form-control" id="folderNameUpdate" placeholder="Enter new folder name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveFolderChangesBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Saving Changes -->
    <div class="modal fade" id="saveChangesModal" tabindex="-1" aria-labelledby="saveChangesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeOrderModalLabel">Change Order of Files and Folders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- JTree Structure Here -->
                    <div id="fileTree" class="jstree"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    {{-- hidden value show the current fodler we are right now --}}
    <input type="hidden" id="currentFolderId" value="">

@endsection


@section('script2')
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        $(document).on('click','#UploadNewFile',function(){
            var myModal = new bootstrap.Modal(document.getElementById('uploadFileModal'));
            myModal.show();
        });

        $(document).on('click','#createNewFolder',function(){
            var myModal = new bootstrap.Modal(document.getElementById('createFolderModal'));
            myModal.show();
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
    <script>
        var treeData = @json($filesAndFolders);

        function getChildren(parentId) {
            return treeData.filter(node => node.parentId === parentId);
        }

        function deleteFolder(id, event) {
            // Prevent the updateFolderContent function from being triggered
            event.stopPropagation();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use the passed folder ID (from data-id)
                    var folderId = id;

                    // Add your logic here to delete the folder via an API call or other action
                    // alert("Folder ID to delete:" + folderId);

                    // Example: AJAX request to delete folder
                    $.ajax({
                        url: '{{route('fileM.submit')}}',
                        method: 'POST',
                        data:  {
                            itemId: folderId,
                            type: "delete",
                            _token: '{{ csrf_token() }}',
                        },  // Convert data to JSON
                        success: function(response) {
                            // Handle successful folder creation
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                            showAlertS(response.parentId); // Assuming response includes the parent folder ID
                        },
                        error: function(xhr, status, error) {
                            alert('Error deleting folder:', error);
                        }
                    });
                }
            });
        }

        // Function to handle deleting file
        function deleteFile(id, event) {
            // Prevent the updateFolderContent function from being triggered
            event.stopPropagation();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use the passed file ID (from data-id)
                    var fileId = id;

                    // Add your logic here to delete the file via an API call or other action
                    alert("File ID to delete:" + fileId);

                    // Example: AJAX request to delete file
                    $.ajax({
                        url: '{{route('fileM.submit')}}',  // Your endpoint for creating a folder
                        method: 'POST',
                        data:  {
                            itemId: fileId,
                            type: "delete",
                            _token: '{{ csrf_token() }}',
                        },  // Convert data to JSON
                        success: function(response) {
                            // Handle successful folder creation
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                            showAlertS(response.parentId); // Assuming response includes the parent folder ID
                        },
                        error: function(xhr, status, error) {
                            alert('Error deleting folder:', error);
                        }
                    });
                }
            });
        }

        function openUpdateFolderModal(element) {
            // Get the folder ID and name from the clicked element
            var folderId = $(element).data('id'); // Get the folder ID
            var folderName = $(element).data('name'); // Get the current folder name

            // Set the hidden input value and folder name in the modal
            $('#folderIdForUpdate').val(folderId);
            $('#folderNameUpdate').val(folderName);

            // Show the modal
            $('#updateFolderModal').modal('show');
        }

        // Extra fucntion for hand cick event additional
        function handleClickAndTrigger(event, element) {
            event.stopPropagation();
            event.preventDefault();

            // Manually trigger the click event on the same element
            if (element) {
                openUpdateFolderModal(element); // Call the function to open the modal
            }
        }

        function updateFolderContent(parentId) {
            var folderContainer = document.getElementById('FolderContainer');
            var fileContainer = document.getElementById('FileContainer');

            if (!folderContainer || !fileContainer) {
                return;
            }

            folderContainer.innerHTML = '';
            fileContainer.innerHTML = '';

            // Set the hidden input value to the current folder ID
            document.getElementById('currentFolderId').value = parentId;

            var children = getChildren(parentId);

            // Add folders and files
            children.forEach(child => {
                if (child.isFile === 0) {
                    folderContainer.innerHTML += `
                        <div class="col mb-3 folder FileFolderItem" data-id="${child.id}">
                            <div class="card shadow selectableItemsContainer " >
                                <div class="row row-cols-3 d-flex align-items-center" style="height: 64px;" onclick="updateFolderContent(${child.id})">
                                    <div class="col col-3 d-flex align-items-center" style="height: 100%;">
                                        <img src="{{asset('img/icons/FileManagement/folder.png')}}"
                                            class="folder-icon p-2 pt-0 pb-0" alt="Folder Icon" height="85%" width="auto">
                                    </div>
                                    <div class="col col-7">
                                        <span class="folder-name">${child.name}</span>
                                    </div>
                                    <div class="col col-2 d-flex align-items-center justify-content-end" style="height: 100%;">
                                        <div class="dropdown m-2 mt-0 mb-0">
                                            <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li ><a class="dropdown-item updateFolderBtn" href="#" data-id="${child.id}" data-name="${child.name}" onclick="handleClickAndTrigger(event,this);">Update Folder Name</a></li>
                                                <li ><a class="dropdown-item text-danger DeleteFolderBtn" href="#" data-id="${child.id}" onclick="deleteFolder(${child.id}, event);">Delete Folder</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (child.isFile === 1) {
                    fileContainer.innerHTML += `
                        <div class="col mb-3 FileFolderItem" data-id="${child.id}">
                            <div class="card shadow">
                                <div class="row row-cols-1 d-flex align-items-center " style="height: 180px;">
                                    <div class="col d-flex justify-content-end align-items-center" style="height: 20%;">
                                        <div class="dropdown m-2 mt-3 mb-0">
                                            <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item updateFileBtn" href="#" data-id="${child.id}" data-name="${child.name}" >Update File Name</a></li>
                                                <li><a class="dropdown-item text-danger DeleteFileBtn" href="#" data-id="${child.id}" onclick="deleteFile(${child.id}, event);">Delete File</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col d-flex justify-content-center align-items-center" style="height: 50%;">
                                        <img src="{{asset('img/icons/FileManagement/pdf.png')}}"
                                            class="folder-icon p-2" alt="File Icon" height="100%" width="auto">
                                    </div>
                                    <div class="col d-flex justify-content-center" style="height: 30%;">
                                        <a class="text-center" href="#">${child.name}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            // Always add "Create New Folder" button at the end
            folderContainer.innerHTML += `
                <div class="col folder mb-3">
                    <div class="card shadow selectableItemsContainer" style="background-color: var(--primary-color-light);" id="createNewFolder" >
                        <div class="row row-cols-2 d-flex align-items-center" style="height: 64px;">
                            <div class="col col-4 d-flex justify-content-end align-items-center" style="height: 100%;">
                                <img src="{{asset('img/icons/FileManagement/add.png')}}"
                                    class="folder-icon p-2 pt-0 pb-0" alt="Folder Icon" height="40%">
                            </div>
                            <div class="col">
                                <span class="folder-name" style="color: #65BEFF;"><b>Create New Folder</b></span>
                            </div>
                        </div>
                    </div>
                </div>
            `;


            fileContainer.innerHTML += `
                <div class="col mb-3">
                    <div class="card shadow theContainerForAddingNewItem" style="background-color: var(--primary-color-light);;" id="UploadNewFile">
                        <div class="row pt-3 row-cols-1 d-flex align-items-center" style="height: 180px;">
                            <div class="col d-flex justify-content-center align-items-center" style="height: 60%;">
                                <img src="{{asset('img/icons/FileManagement/new-file.png')}}"
                                    class="folder-icon p-2" alt="Upload Icon" height="100%" width="auto">
                            </div>
                            <div class="col d-flex justify-content-center" style="height: 40%;">
                                <span class="folder-name" style="color: #65BEFF;"><b>Upload New File</b></span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            updateBreadcrumb(parentId);
        }

        function updateBreadcrumb(currentFolderId) {
            var breadcrumb = document.querySelector('.breadcrumb');
            breadcrumb.innerHTML = '';

            // Set the hidden input value to the current folder ID
            document.getElementById('currentFolderId').value = currentFolderId;

            let currentNode = treeData.find(node => node.id === currentFolderId);
            var path = [];

            while (currentNode) {
                path.unshift(currentNode);
                currentNode = treeData.find(node => node.id === currentNode.parentId);
            }

            path.forEach((node, index) => {
                if (index > 0) {
                    breadcrumb.innerHTML += `
                        <li class="breadcrumb-item">
                            <a href="#" onclick="updateFolderContent(${node.id})" class="d-flex align-items-center">
                                <img src="${node.isFile === 0 ? '{{asset('img/icons/FileManagement/folderBlue.png')}}' : '{{asset('img/icons/FileManagement/building.png')}}'}" alt="Icon" height="20px" class="m-2 mt-0 mb-0">${node.name}
                            </a>
                        </li>
                    `;
                } else {
                    breadcrumb.innerHTML += `
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="#" onclick="updateFolderContent(${node.id})" class="d-flex align-items-center">
                                <img src="{{asset('img/icons/FileManagement/building.png')}}" alt="Home Icon" height="30px" class="m-2 mt-0 mb-0">Root
                            </a>
                        </li>
                    `;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateFolderContent(1); // Load content for root folder
        });

        function convertToJSTreeFormat(treeData) {
            var nodesMap = {};

            // Initialize the nodesMap with the input data
            treeData.forEach(item => {
                var icon = item.isFile ? "jstree-file" : "jstree-folder";
                nodesMap[item.id] = {
                    text: item.name,
                    children: [],
                    icon: icon,
                    isFile: item.isFile
                };
            });

            // Build the tree structure
            var jstreeData = [];
            for (var id in nodesMap) {
                var node = nodesMap[id];
                var parentId = treeData.find(item => item.id == id).parentId; // Correctly get parentId
                if (parentId === null) {
                    jstreeData.push(node); // Root nodes
                } else {
                    nodesMap[parentId].children.push(node); // Add child to parent
                }
            }
            return jstreeData;
        }

        // Convert and initialize jsTree
        $(document).ready(function() {
            var jstreeData = convertToJSTreeFormat(treeData);
            console.log(jstreeData);
        });

        $(document).ready(function() {
            // Sample data for the jsTree with isFile property
            const data = convertToJSTreeFormat(treeData);
            // [
            //     { "text": "Folder 1", "children": [
            //         { "text": "File 1-1", "icon": "jstree-file", "isFile": true },
            //         { "text": "File 1-2", "icon": "jstree-file", "isFile": true }
            //     ], "icon": "jstree-folder", "isFile": false },
            //     { "text": "Folder 2", "children": [
            //         { "text": "File 2-1", "icon": "jstree-file", "isFile": true },
            //         { "text": "File 2-2", "icon": "jstree-file", "isFile": true }
            //     ], "icon": "jstree-folder", "isFile": false },
            //     { "text": "Folder 3", "children": [], "icon": "jstree-folder", "isFile": false }
            // ];

            // Initialize jsTree
            $('#fileTree').jstree({
                'core': {
                    'data': data,
                    'check_callback': function (operation, node, parent, position, more) {
                        // Allow adding folders and files
                        if (operation === "create_node") {
                            if (node && node.a_attr && node.a_attr.isFile) {
                                return false; // Prevent creating nodes under files
                            }
                        }
                        return true; // Allow other operations
                    }
                },
                'plugins': ['dnd'], // Enable drag and drop
                'dnd': {
                    'check_while_dragging': true,
                    'is_draggable': function(node) {
                        return true; // Allow all nodes to be draggable
                    }
                }
            }).on("ready.jstree", function() {
                // Set icons based on isFile attribute
                $('#fileTree').jstree(true).get_json('#', { flat: true }).forEach(function(node) {
                    if (node.a_attr && node.a_attr.isFile) {
                        $('#fileTree').jstree(true).rename_node(node.id, node.text);
                    }
                });
            });

            // Prevent dropping files into files or folders into folders
            $('#fileTree').on("move_node.jstree", function (e, data) {
                const node = data.node;
                const parentNode = data.parent;

                if (node.a_attr && node.a_attr.isFile && (parentNode.a_attr && parentNode.a_attr.isFile)) {
                    // If trying to drop a file into a file, revert the action
                    $('#fileTree').jstree("move_node", node, data.old_parent, data.old_position);
                    alert("Cannot move files into other files!");
                    return; // Prevent further execution
                } else if (!node.a_attr.isFile && (parentNode.a_attr && parentNode.a_attr.isFile)) {
                    // If trying to drop a folder into a file, revert the action
                    $('#fileTree').jstree("move_node", node, data.old_parent, data.old_position);
                    alert("Cannot move folders into files!");
                    return; // Prevent further execution
                }

                // Optional: Change icon of the parent node if valid drop occurs
                if (parentNode.a_attr && !parentNode.a_attr.isFile) {
                    $('#fileTree').jstree().get_node(parentNode).icon = "jstree-folder";
                }
            });
        });

        // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // fetch loop update real time

        function fetch() {
            $.ajax({
                url: '{{ route('fileM.json.fetch') }}',
                method: 'GET',
                success: function(response) {
                    updateListFilesAndFolders(response);
                    //  get all the data & update content
                },
                error: function(error) {
                    console.error('Error fetching :', error);
                }
            });
        }

        function updateListFilesAndFolders(response) {
            var currentFolderId = document.getElementById('currentFolderId').value;
            treeData = response.filesAndFolders;

            // Get the new items from the database
            // ------------------------------------------------------------------
            var childrenOfCurrentParent = treeData.filter(item => item.parentId === parseInt(currentFolderId));

            // Create a mapping of the new names for easy lookup
            const newNameMap = {};
            const newIdsSet = new Set(); // Create a set to hold the new IDs

            childrenOfCurrentParent.forEach(child => {
                newNameMap[child.id] = child.name; // Map the new name
                newIdsSet.add(child.id); // Add the new ID to the set
            });

            // Get the existing items
            const items = document.querySelectorAll('.FileFolderItem');

            // Iterate over the existing items to update their names or remove them
            Array.from(items).forEach(item => {
                const id = item.getAttribute('data-id'); // Get the id from the data-id attribute
                const newName = newNameMap[id]; // Check if there's a new name for this ID

                // If a new name exists, update the item name in the DOM
                if (newName) {
                    if (item.querySelector('.folder-name')) {
                        item.querySelector('.folder-name').textContent = newName; // For folders
                    } else {
                        const link = item.querySelector('a.text-center');
                        if (link) {
                            link.textContent = newName; // For files
                        }
                    }
                } else {
                    // If the item ID does not exist in the new data, remove it from the DOM
                    item.remove(); // Remove the item from the DOM
                }
            });

            // Add new items that are not currently in the DOM
            childrenOfCurrentParent.forEach(child => {
                const existingItem = Array.from(items).find(item => item.getAttribute('data-id') === child.id.toString());

                // Only add the item if it doesn't already exist in the DOM
                if (!existingItem) {
                    const parentContainer = document.getElementById(child.isFile === 1 ? 'FileContainer' : 'FolderContainer'); // Get the parent container based on the child's parentId
                    let newItemHTML;

                    // Create HTML for the new item based on whether it is a file or folder
                    if (child.isFile === 0) { // Folder
                        newItemHTML = `
                            <div class="col mb-3 folder FileFolderItem" data-id="${child.id}">
                                <div class="card shadow selectableItemsContainer">
                                    <div class="row row-cols-3 d-flex align-items-center" style="height: 64px;" onclick="updateFolderContent(${child.id})">
                                        <div class="col col-3 d-flex align-items-center" style="height: 100%;">
                                            <img src="{{asset('img/icons/FileManagement/folder.png')}}"
                                                class="folder-icon p-2 pt-0 pb-0" alt="Folder Icon" height="85%" width="auto">
                                        </div>
                                        <div class="col col-7">
                                            <span class="folder-name">${child.name}</span>
                                        </div>
                                        <div class="col col-2 d-flex align-items-center justify-content-end" style="height: 100%;">
                                            <div class="dropdown m-2 mt-0 mb-0">
                                                <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                    <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li ><a class="dropdown-item updateFolderBtn" href="#" data-id="${child.id}" data-name="${child.name}" onclick="handleClickAndTrigger(event,this);">Update Folder Name</a></li>
                                                    <li ><a class="dropdown-item text-danger DeleteFolderBtn" href="#" data-id="${child.id}" onclick="deleteFolder(${child.id}, event);">Delete Folder</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else if (child.isFile === 1) { // File
                        newItemHTML = `
                            <div class="col mb-3 FileFolderItem"  data-id="${child.id}">
                                <div class="card shadow">
                                    <div class="row row-cols-1 d-flex align-items-center" style="height: 180px;">
                                        <div class="col d-flex justify-content-end align-items-center" style="height: 20%;">
                                            <div class="dropdown m-2 mt-3 mb-0">
                                                <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                    <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item updateFileBtn" href="#" data-id="${child.id}" data-name="${child.name}">Update File Name</a></li>
                                                    <li><a class="dropdown-item text-danger DeleteFileBtn" href="#" data-id="${child.id}" onclick="deleteFile(${child.id}, event);">Delete File</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col d-flex justify-content-center align-items-center" style="height: 50%;">
                                            <img src="{{asset('img/icons/FileManagement/pdf.png')}}"
                                                class="folder-icon p-2" alt="File Icon" height="100%" width="auto">
                                        </div>
                                        <div class="col d-flex justify-content-center" style="height: 30%;">
                                            <a class="text-center" href="#">${child.name}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    // Prepend the new item to the parent container in the DOM
                    if (parentContainer) {
                        parentContainer.insertAdjacentHTML('afterbegin', newItemHTML); // Add the new item HTML to the beginning
                    }
                }
            });

            // (Optional) Log the updated item list
            const updatedItems = Array.from(document.querySelectorAll('.FileFolderItem')).map(item => {
                const id = item.getAttribute('data-id'); // Get the id from the data-id attribute
                let name;

                // Check if it's a folder or file to get the correct name element
                if (item.querySelector('.folder-name')) {
                    name = item.querySelector('.folder-name').textContent.trim(); // For folders
                } else {
                    name = item.querySelector('a.text-center').textContent.trim(); // For files
                }

                return { id, name }; // Return an object with id and name
            });
        }

        setInterval(fetch, 3000); // Call every second
    </script>

    <script>
        $('#uploadFileBtn').on('click', function() {
            var folderId = $('#currentFolderId').val();
            var fileInput = $('#fileInput')[0].files[0];

            // Initialize FormData
            var formData = new FormData();
            formData.append('folder_id', folderId);
            formData.append('file', fileInput);
            formData.append('type', "NFile");

            // Set up AJAX with CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            console.log('file : ' + folderId);

            $.ajax({
                url: '{{ route('fileM.submit') }}',  // Your endpoint for creating a folder
                method: 'POST',
                data: formData,
                processData: false,  // Important for FormData
                contentType: false,   // Important for FormData
                success: function(response) {
                    // Handle successful upload
                    showAlertS("File uploaded successfully");
                    // Optionally close the modal
                    $('#uploadFileModal').modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle error
                    showAlertD("File upload failed");
                }
            });
        });


        $('#createFolderBtn').on('click', function() {
            var folderId = $('#currentFolderId').val();
            var folderName = $('#folderName').val();
            // 123
            $.ajax({
                url: '{{route('fileM.submit')}}',  // Your endpoint for creating a folder
                method: 'POST',
                data:  {
                    folderId: folderId,
                    name: folderName,
                    type: "NFolder",
                    _token: '{{ csrf_token() }}',
                },  // Convert data to JSON
                success: function(response) {
                    // Handle successful folder creation
                    showAlertS("Folder created successfully");
                    // Optionally close the modal
                    $('#createFolderModal').modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle error
                    showAlertD("Folder creation failed");
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '.updateFileBtn', function() {
            var fileId = $(this).data('id'); // Get the file ID
            var fileName = $(this).data('name'); // Get the current file name

            // Set the hidden input value and file name in the modal
            $('#fileIdForUpdate').val(fileId);
            $('#fileName').val(fileName);

            // Show the modal
            $('#updateFileModal').modal('show');
        });

        // Open the Update Folder Modal
        $(document).on('click', '.updateFolderBtn', function() {
            var folderId = $(this).data('id'); // Get the folder ID
            var folderName = $(this).data('name'); // Get the current folder name

            // Set the hidden input value and folder name in the modal
            $('#folderIdForUpdate').val(folderId);
            $('#folderNameUpdate').val(folderName);

            // Show the modal
            $('#updateFolderModal').modal('show');
        });

        $(document).on('click','#saveFileChangesBtn', function() {
            var fileId = $('#fileIdForUpdate').val(); // Get file ID
            var newFileName = $('#fileName').val(); // Get the new file name

            $.ajax({
                url: '{{route('fileM.submit')}}',
                method: 'POST',
                data:  {
                    id: fileId,
                    name: newFileName,
                    type: 'update',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlertS('File name updated successfully!');
                    $('#updateFileModal').modal('hide');
                    // Optionally refresh the page or update the UI
                },
                error: function(xhr) {
                    showAlertD('Error updating file name.');
                }
            });
        });

        // Save Folder Changes
        $(document).on('click', '#saveFolderChangesBtn', function() {
            var folderId = $('#folderIdForUpdate').val(); // Get folder ID
            var newFolderName = $('#folderNameUpdate').val(); // Get the new folder name
            console.log(folderId);
            console.log(newFolderName);
            $.ajax({
                url: '{{route('fileM.submit')}}',
                method: 'POST',
                data:  {
                    id: folderId,
                    name: newFolderName,
                    type: 'update',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlertS('Folder name updated successfully!');
                    $('#updateFolderModal').modal('hide');
                    // Optionally refresh the page or update the UI
                },
                error: function(xhr) {
                    showAlertD('Error updating folder name.');
                }
            });
        });
    </script>
@endsection
