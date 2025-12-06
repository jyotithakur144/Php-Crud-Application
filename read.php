<?php
require "conn.php";

// Determine current sort order
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Toggle order for next click
function sort_order($currentOrder) {
    return $currentOrder === 'ASC' ? 'DESC' : 'ASC';
}

// Allowed sorting and order values
// $allowedSort = ['id', 'name', 'email', 'gender', 'status'];
// $allowedOrder = ['ASC', 'DESC'];
// if (!in_array($sort, $allowedSort)) $sort = 'id';
// if (!in_array($order, $allowedOrder)) $order = 'ASC';

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// Search
$search = '';
$where = '';
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where = "WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
}

// Fetch users
$sql = "SELECT * FROM users $where ORDER BY $sort $order LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);
?>

<h2>User List</h2>

<!-- Search Form -->
<form method="get">
    <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
    <button>Search</button>
</form>

<a href="create.php">Add New User</a><br><br>

<table border="1" cellpadding="10">
<tr>
    <th><a href="?sort=id&order=<?= $sort=='id'?sort_order($order):'ASC' ?>">ID</a></th>
    <th><a href="?sort=name&order=<?= $sort=='name'?sort_order($order):'ASC' ?>">Name</a></th>
    <th><a href="?sort=email&order=<?= $sort=='email'?sort_order($order):'ASC' ?>">Email</a></th>
    <th>File</th>
    <th><a href="?sort=gender&order=<?= $sort=='gender'?sort_order($order):'ASC' ?>">Gender</a></th>
    <th>Hobbies</th>
    <th><a href="?sort=status&order=<?= $sort=='status'?sort_order($order):'ASC' ?>">Status</a></th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td>
        <?php 
        if(!empty($row['file'])) 
            echo '<img src="uploads/'.htmlspecialchars($row['file']).'" width="80" style="object-fit:cover;">'; 
        ?>
    </td>
    <td><?= htmlspecialchars($row['gender']) ?></td>
    <td><?= htmlspecialchars($row['hobbies']) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td>
        <a href="update.php?id=<?= $row['id'] ?>">Edit</a>
        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete User?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<?php
// Pagination links
$total_rows = mysqli_fetch_array(mysqli_query($conn,"SELECT COUNT(*) FROM users $where"))[0];
$total_pages = ceil($total_rows / $limit);

for($i=1; $i<=$total_pages; $i++){
    $link = "read.php?page=$i&sort=$sort&order=$order";
    if($search !== '') $link .= "&search=".urlencode($search);
    echo $i == $page ? "<strong>$i</strong> " : "<a href='$link'>$i</a> ";
}
?>
