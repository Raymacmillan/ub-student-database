<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';

$db = getDB();

// ─── CREATE ───────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    if ($_POST['action'] === 'create') {
        $id   = trim($_POST['id']);
        $name = trim($_POST['name']);

        if (empty($id) || empty($name)) {
            header("Location: manage_database.php?msg=Student+ID+and+Name+are+required&type=danger");
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO test (id, name) VALUES (?, ?)");
                $stmt->execute([$id, $name]);
                header("Location: manage_database.php?msg=Student+added+successfully&type=success");
            } catch (PDOException $e) {
                header("Location: manage_database.php?msg=Error:+Student+ID+already+exists&type=danger");
            }
        }
        exit;
    }

    elseif ($_POST['action'] === 'update') {
        $original_id = trim($_POST['original_id']);
        $new_id      = trim($_POST['id']);
        $name        = trim($_POST['name']);

        if (empty($new_id) || empty($name)) {
            header("Location: manage_database.php?msg=ID+and+Name+cannot+be+empty&type=danger");
        } else {
            try {
                $stmt = $db->prepare("UPDATE test SET id = ?, name = ? WHERE id = ?");
                $stmt->execute([$new_id, $name, $original_id]);
                header("Location: manage_database.php?msg=Record+updated+successfully&type=success");
            } catch (PDOException $e) {
                header("Location: manage_database.php?msg=Error:+Student+ID+already+exists&type=danger");
            }
        }
        exit;
    }

    elseif ($_POST['action'] === 'delete') {
        $id = trim($_POST['id']);
        $stmt = $db->prepare("DELETE FROM test WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: manage_database.php?msg=Record+deleted&type=success");
        exit;
    }
}

// ─── READ ─────────────────────────────────────────
$message      = isset($_GET['msg'])    ? $_GET['msg']    : '';
$message_type = isset($_GET['type'])   ? $_GET['type']   : '';
$search       = isset($_GET['search']) ? trim($_GET['search']) : '';
$edit_id      = isset($_GET['edit'])   ? $_GET['edit']   : null;

if (!empty($search)) {
    $stmt = $db->prepare("SELECT * FROM test WHERE id LIKE ? OR name LIKE ? ORDER BY name");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $db->query("SELECT * FROM test ORDER BY name");
}

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$edit_student = null;
if ($edit_id) {
    $stmt = $db->prepare("SELECT * FROM test WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_student = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students — UB Database</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <div class="crest">UB</div>
        Student Database
    </div>
    <div class="navbar-links">
        <span class="user-tag">Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</nav>

<div class="page">
    <div class="page-header">
        <h1>Student Records</h1>
        <p>Create, view, update and delete student records.</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="layout">

        <!-- Add / Edit Form -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2><?php echo $edit_student ? 'Edit Student' : 'Add Student'; ?></h2>
                    <?php if ($edit_student): ?>
                        <a href="manage_database.php" class="btn btn-outline btn-sm">Cancel</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form action="manage_database.php" method="POST">
                        <input type="hidden" name="action" value="<?php echo $edit_student ? 'update' : 'create'; ?>">
                        <?php if ($edit_student): ?>
                            <input type="hidden" name="original_id" value="<?php echo htmlspecialchars($edit_student['id']); ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Student ID</label>
                            <input
                                type="text"
                                name="id"
                                maxlength="15"
                                placeholder="e.g. 20010001"
                                value="<?php echo $edit_student ? htmlspecialchars($edit_student['id']) : ''; ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input
                                type="text"
                                name="name"
                                maxlength="50"
                                placeholder="e.g. Ray Macmillan"
                                value="<?php echo $edit_student ? htmlspecialchars($edit_student['name']) : ''; ?>"
                            >
                        </div>

                        <button type="submit" class="btn <?php echo $edit_student ? 'btn-primary' : 'btn-success'; ?> btn-full">
                            <?php echo $edit_student ? 'Update Record' : 'Add Student'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2>All Students <span class="badge"><?php echo count($students); ?></span></h2>
                </div>
                <div class="card-body">
                    <form action="manage_database.php" method="GET" class="search-bar">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search by name or ID..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                        <button type="submit" class="btn btn-primary">Search</button>
                        <?php if (!empty($search)): ?>
                            <a href="manage_database.php" class="btn btn-outline">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="table-wrapper">
                    <?php if (empty($students)): ?>
                        <div class="empty-state">
                            <div class="icon">🔍</div>
                            <p>No students found.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Full Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td>
                                            <div class="actions">
                                                <a href="manage_database.php?edit=<?php echo urlencode($student['id']); ?>"
                                                   class="btn btn-outline btn-sm">Edit</a>
                                                <form action="manage_database.php" method="POST"
                                                      onsubmit="return confirm('Delete <?php echo htmlspecialchars($student['name']); ?>?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>