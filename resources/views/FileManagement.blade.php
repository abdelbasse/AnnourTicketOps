@extends('layouts')


@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <style>
        .dropdown-btn::after {
            display: none;
            width: 24px;
            height: 24px;
        }

        .theContainerForAddingNewItem{
            cursor: pointer;
        }
    </style>
@endsection

@section('body')
    <div class="container">
        <!-- Breadcrumb -->
        <div class="row d-flex justify-content-between">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-flex align-items-center">
                        <li class="breadcrumb-item">
                            <a href="##" class="d-flex align-items-center">
                                <img src="https://cdn-icons-png.flaticon.com/128/14034/14034653.png" alt="Home Icon" height="30px" class="m-2 mt-0 mb-0">Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="" class="d-flex align-items-center">
                                <img src="https://cdn-icons-png.flaticon.com/128/14090/14090367.png" alt="Folder Icon" height="20px" class="m-2 mt-0 mb-0">Folders0
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="" class="d-flex align-items-center">
                                <img src="https://cdn-icons-png.flaticon.com/128/14090/14090367.png" alt="Folder Icon" height="20px" class="m-2 mt-0 mb-0">Folders1
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
        <div class="row row-cols-1 row-cols-lg-4 row-cols-md-2 row-cols-sm-2" id="FolderContainer">
            <div class="col col-md-2 folder mb-3">
                <div class="card shadow">
                    <div class="row row-cols-3 d-flex align-items-center" style="height: 64px;">
                        <div class="col col-3 d-flex align-items-center" style="height: 100%;">
                            <img src="https://cdn-icons-png.flaticon.com/128/3735/3735057.png"
                                class="folder-icon p-2 pt-0 pb-0" alt="Folder Icon" height="85%" width="auto">
                        </div>
                        <div class="col col-7">
                            <span class="folder-name">1. Resource</span>
                        </div>
                        <div class="col col-2 d-flex align-items-center justify-content-end" style="height: 100%;">
                            <div class="dropdown m-2 mt-0 mb-0">
                                <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateFolderModal">Update Folder Name</a></li>
                                    <li><a class="dropdown-item text-danger DeleteFolderBtn" href="#" >Delete Folder</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- New Folder Button Modal -->
            <div class="col col-md-2 folder mb-3">
                <div class="card shadow theContainerForAddingNewItem" style="background-color: #F7F7F7;" id="createNewFolder">
                    <div class="row row-cols-2 d-flex align-items-center" style="height: 64px;">
                        <div class="col col-4 d-flex justify-content-end align-items-center" style="height: 100%;">
                            <img src="https://cdn-icons-png.flaticon.com/128/10337/10337471.png"
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
        <div class="row" id="FileContainer">
            <div class="col col-md-2 mb-3">
                <div class="card shadow">
                    <div class="row row-cols-1 d-flex align-items-center" style="height: 180px;">
                        <div class="col d-flex justify-content-end align-items-center " style="height: 20%;">
                            <div class="dropdown m-2 mt-3 mb-0">
                                <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateFileModal">Update File Name</a></li>
                                    <li><a class="dropdown-item text-danger DeleteFileBtn" href="#">Delete File</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col d-flex justify-content-center align-items-center" style="height: 50%;">
                            <img src="https://cdn-icons-png.flaticon.com/128/4726/4726010.png"
                                class="folder-icon p-2" alt="File Icon" height="100%" width="auto">
                        </div>
                        <div class="col d-flex justify-content-center" style="height: 30%;">
                            <a class="text-center" href="">1. Resource nsd jbsif wif bwfi y.pdf</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Upload New File Button Modal -->
            <div class="col col-md-2 mb-3" >
                <div class="card shadow theContainerForAddingNewItem" style="background-color: #F7F7F7;" id="UploadNewFile">
                    <div class="row pt-3 row-cols-1 d-flex align-items-center" style="height: 180px;">
                        <div class="col d-flex justify-content-center align-items-center" style="height: 60%;">
                            <img src="https://cdn-icons-png.flaticon.com/128/10819/10819554.png"
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
                    <form>
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Choose File</label>
                            <input class="form-control" type="file" id="fileInput">
                        </div>
                        <div class="mb-3">
                            <label for="fileDescription" class="form-label">Description</label>
                            <input type="text" class="form-control" id="fileDescription" placeholder="Enter file description">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Upload File</button>
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
                    <button type="button" class="btn btn-primary">Create Folder</button>
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
                        <div class="mb-3">
                            <label for="fileName" class="form-label">New File Name</label>
                            <input type="text" class="form-control" id="fileName" placeholder="Enter new file name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
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
                        <div class="mb-3">
                            <label for="folderName" class="form-label">New Folder Name</label>
                            <input type="text" class="form-control" id="folderName" placeholder="Enter new folder name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Saving Changes -->
    <div class="modal fade" id="saveChangesModal" tabindex="-1" aria-labelledby="saveChangesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveChangesModalLabel">Save Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You have unsaved changes. Do you want to save them before closing?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script2')
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // SweetAlert2 for delete file confirmation
        $(document).on('click', '.DeleteFileBtn', function () {
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
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                }
            })
        });

        // SweetAlert2 for delete folder confirmation
        $(document).on('click' ,'.DeleteFolderBtn', function () {
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
                    Swal.fire(
                        'Deleted!',
                        'Your folder has been deleted.',
                        'success'
                    )
                }
            })
        });

        $(document).on('click','#UploadNewFile',function(){
            var myModal = new bootstrap.Modal(document.getElementById('uploadFileModal'));
            myModal.show();
        });

        $(document).on('click','#createNewFolder',function(){
            var myModal = new bootstrap.Modal(document.getElementById('createFolderModal'));
            myModal.show();
        });
    </script>

    <script>
        const treeData = [
            { id: 1, parentId: null, name: "Root", type: "folder" },
            { id: 2, parentId: 1, name: "Folder1", type: "folder" },
            { id: 3, parentId: 1, name: "Folder2", type: "folder" },
            { id: 4, parentId: 1, name: "File0.txt", type: "file" },
            { id: 5, parentId: 2, name: "File1.txt", type: "file" },
            { id: 6, parentId: 2, name: "File2.docx", type: "file" },
            { id: 7, parentId: 3, name: "File3.pdf", type: "file" },
            { id: 8, parentId: 3, name: "Folder3", type: "folder" },
        ];

        function getChildren(parentId) {
            return treeData.filter(node => node.parentId === parentId);
        }

        function updateFolderContent(parentId) {
            const folderContainer = document.getElementById('FolderContainer');
            const fileContainer = document.getElementById('FileContainer');

            folderContainer.innerHTML = '';
            fileContainer.innerHTML = '';

            const children = getChildren(parentId);

            // Check if there are any files
            const hasFiles = children.some(child => child.type === 'file');
            // Check if there are any folders
            const hasFolders = children.some(child => child.type === 'folder');

            // Add folders and files
            children.forEach(child => {
                if (child.type === 'folder') {
                    folderContainer.innerHTML += `
                        <div class="col col-md-2 mb-3 folder">
                            <div class="card shadow">
                                <div class="row row-cols-3 d-flex align-items-center" style="height: 64px;" onclick="updateFolderContent(${child.id})">
                                    <div class="col col-3 d-flex align-items-center" style="height: 100%;">
                                        <img src="https://cdn-icons-png.flaticon.com/128/3735/3735057.png"
                                            class="folder-icon p-2 pt-0 pb-0" alt="Folder Icon" height="85%" width="auto">
                                    </div>
                                    <div class="col col-7">
                                        <span class="folder-name">${child.name}</span>
                                    </div>
                                    <div class="col col-2 d-flex align-items-center justify-content-end" style="height: 100%;">
                                        <div class="dropdown m-2 mt-0 mb-0">
                                            <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateFolderModal">Update Folder Name</a></li>
                                                <li><a class="dropdown-item text-danger DeleteFolderBtn" href="#">Delete Folder</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (child.type === 'file') {
                    fileContainer.innerHTML += `
                        <div class="col col-md-2 mb-3">
                            <div class="card shadow">
                                <div class="row row-cols-1 d-flex align-items-center" style="height: 180px;">
                                    <div class="col d-flex justify-content-end align-items-center" style="height: 20%;">
                                        <div class="dropdown m-2 mt-3 mb-0">
                                            <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateFileModal">Update File Name</a></li>
                                                <li><a class="dropdown-item text-danger DeleteFileBtn" href="#">Delete File</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col d-flex justify-content-center align-items-center" style="height: 50%;">
                                        <img src="https://cdn-icons-png.flaticon.com/128/4726/4726010.png"
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
                <div class="col col-md-2 folder mb-3">
                    <div class="card shadow theContainerForAddingNewItem" style="background-color: #F7F7F7;" id="createNewFolder">
                        <div class="row row-cols-2 d-flex align-items-center" style="height: 64px;">
                            <div class="col col-4 d-flex justify-content-end align-items-center" style="height: 100%;">
                                <img src="https://cdn-icons-png.flaticon.com/128/10337/10337471.png"
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
                <div class="col col-md-2 mb-3">
                    <div class="card shadow theContainerForAddingNewItem" style="background-color: #F7F7F7;" id="UploadNewFile">
                        <div class="row pt-3 row-cols-1 d-flex align-items-center" style="height: 180px;">
                            <div class="col d-flex justify-content-center align-items-center" style="height: 60%;">
                                <img src="https://cdn-icons-png.flaticon.com/128/10819/10819554.png"
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
            const breadcrumb = document.querySelector('.breadcrumb');
            breadcrumb.innerHTML = '';

            let currentNode = treeData.find(node => node.id === currentFolderId);
            const path = [];

            while (currentNode) {
                path.unshift(currentNode);
                currentNode = treeData.find(node => node.id === currentNode.parentId);
            }

            path.forEach((node, index) => {
                if (index > 0) {
                    breadcrumb.innerHTML += `
                        <li class="breadcrumb-item">
                            <a href="#" onclick="updateFolderContent(${node.id})" class="d-flex align-items-center">
                                <img src="${node.type === 'folder' ? 'https://cdn-icons-png.flaticon.com/128/14090/14090367.png' : 'https://cdn-icons-png.flaticon.com/128/4726/4726010.png'}" alt="Icon" height="20px" class="m-2 mt-0 mb-0">${node.name}
                            </a>
                        </li>
                    `;
                } else {
                    breadcrumb.innerHTML += `
                        <li class="breadcrumb-item active"  aria-current="page">
                            <a href="#" onclick="updateFolderContent(${node.id})" class="d-flex align-items-center">
                                <img src="https://cdn-icons-png.flaticon.com/128/14034/14034653.png" alt="Home Icon" height="30px" class="m-2 mt-0 mb-0">Root
                            </a>
                        </li>
                    `;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateFolderContent(1); // Load content for root folder
        });
    </script>
@endsection
