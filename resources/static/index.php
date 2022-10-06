<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css"
          integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="stylesheet" href="https://bootstrap5.ru/css/docs.css">
    <title>index</title>
</head>
<body style="background-color: #eee; display:flex; flex-direction: row;">
<div style="width: 30%; height: 98%; margin: 5px; overflow-y: scroll" class="card rounded-3 text-black">
    <form method="POST" action="handlers/additionHandler.php">
        <label for="email_field" style="margin-left: 5px; margin-top: 5px">Email</label>
        <input type="email" id="email_field" name="email" style="margin: 5px; width: 95%" placeholder="email@domain.com">

        <label for="name_field" style="margin-left: 5px; margin-top: 5px">Your first and last name</label>
        <input type="text" id="name_field" name="name" style="margin: 5px; width: 95%" placeholder="John Smith">

        <input type="hidden" name="edit" id="edit" value="0">

        <div style="margin: 5px; width: 95%">
            <label for="gender_field">Gender</label>
            <select name="gender" id="gender_field" style="width: 100%; margin-top: 5px">
                <option value="1">Male</option>
                <option value="2">Female</option>
            </select>
        </div>

        <div style="margin: 5px; width: 95%">
            <label for="status_field">Status</label>
            <select name="status" id="status_field" style="width: 100%; margin-top: 5px">
                <option value="1">Active</option>
                <option value="2">Inactive</option>
            </select>
        </div>

        <p id="edit_warning" style="display: none">Now editing. Clear form to cancel editing</p>

        <p id="input_warning" style="display: none; color: #4f1100"></p>

        <div style="display: flex; flex-direction: row; margin-top: 20px; justify-content: center">
            <button onclick="return checkInput()" type="submit" class="btn btn-outline-success" style="margin-right: 5px; width: 40%">Submit</button>
            <button onclick="resetForm()" type="reset" class="btn btn-outline-danger" style="margin-left: 5px; width: 40%">Clear form</button>
        </div>
    </form>
</div>
<div style="width: 70%; height: 98%; margin: 5px; overflow-y: scroll" class="card rounded-3 text-black">
    <table style="margin: 10px; width: 98%" class="table table-striped table-bordered">
        <thead>
        <tr>
            <th scope="col">Email</th>
            <th scope="col">Name</th>
            <th scope="col">Gender</th>
            <th scope="col">Status</th>
            <th scope="col">Controls</th>
        </tr>
        </thead>
        <tbody id="table_body">
        <?php
        $tableBody = '';
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        if (!$sock) {
            header('Location: http://localhost/error.php');
            http_response_code(503);
            die();
        }

        $result = socket_connect($sock, '127.0.0.1', 8080);
        if (!$result) {
            header('Location: http://localhost/error.php');
            http_response_code(503);
            die();
        }

        $message = "GET /users/all HTTP1.1\r\n\r\n";
        socket_write($sock, $message, strlen($message));
        $rawData = socket_read($sock, 1024);
        $responseBits = preg_split("/\r\n/", $rawData);
        $json = $responseBits[sizeof($responseBits) - 1];
        //preg_match("/\{(?:[^{}])*\}/g", $rawData, $json);
        socket_close($sock);
        foreach (json_decode($json, true) as $entry) {
            $tableBody .= '<tr id =' . $entry['email'] . '>
                    <td>' . $entry['email'] . "</td>
                    <td id='name'>" . $entry['name'] . "</td>
                    <td id='gender'>" . ucfirst(strtolower($entry['gender'])) . "</td>
                    <td id='status'>" . ucfirst(strtolower($entry['status'])) . "</td>
                    <td>
                        <form action='handlers/deleteHandler.php' style='display: flex; flex-direction: row; width: inherit' method='POST'>
                        <button type='button' onclick='fillForEdit(this)' value='" . $entry['email'] . "'
                        class='btn btn-outline-warning' style='margin: 5px; width=40%'>Edit</button>
                        <button type='submit' value='" . $entry['email'] . "' name='delete'
                        onclick='return confirm(\"This action cannot be undone. Proceed?\")'
                        class='btn btn-outline-danger' style='margin: 5px; width=40%'>Delete</input>
                        </form>
                    </td>
                </tr>";
        }
        echo $tableBody;
        ?>
        </tbody>
    </table>
</div>

<script src='http://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.js'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script type="text/javascript">
    function fillForEdit(editButton) {
        let rowName = editButton.value;
        let row = document.getElementById(rowName);
        let cells = row.children;
        document.getElementById('email_field').value = rowName;
        for (const cell of cells) {
            if (cell.id === 'name') {
                document.getElementById('name_field').value = cell.innerHTML;
            } else if (cell.id === 'gender') {
                if (cell.innerHTML === 'Male') {
                    document.getElementById('gender_field').value = 1;
                } else {
                    document.getElementById('gender_field').value = 2;
                }
            } else if (cell.id === 'status') {
                if (cell.innerHTML === 'Active') {
                    document.getElementById('status_field').value = 1;
                } else {
                    document.getElementById('status_field').value = 2;
                }
            }
        }
        document.getElementById('edit').value = 1;
        let x = document.getElementById("edit_warning");
        if (x.style.display === "none") {
            x.style.display = "block";
        }
    }

    function resetForm() {
        document.getElementById('edit').value = 0;
        let x = document.getElementById("edit_warning");
        let y = document.getElementById('input_warning');
        if (x.style.display === "block") {
            x.style.display = "none";
        }
        if (y.style.display === "block") {
            y.style.display = "none";
        }
    }

    function showWarningMessage(message) {
        let x = document.getElementById('input_warning');
        x.innerHTML = message;
        if (x.style.display === 'none') {
            x.style.display = 'block';
        }
    }

    function checkInput() {
        let email = document.getElementById('email_field').value;
        let name = document.getElementById('name_field').value;
        let warningText = '';

        if (email == null || email === '' || name == null || name === '') {
            warningText += 'All fields must be filled';
            showWarningMessage(warningText);
            return false;
        } else if (!email.match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        )) {
            warningText += 'Invalid email';
            showWarningMessage(warningText);
            return false;
        }
        return true;
    }
</script>

</body>
