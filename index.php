<?php
require_once 'db_connect.php';
session_start();

try {
    // Determine if we need to filter by user
    $is_logged_in = isset($_SESSION['user_id']);
    $user_id_param = $is_logged_in ? $_SESSION['user_id'] : null;

    // --- Search Functionality ---
    $search_query = '';
    $search_where_clause = '';
    $search_params = [];
    if (isset($_GET['search_query']) && !empty(trim($_GET['search_query']))) {
        $search_query = trim($_GET['search_query']);
        $search_where_clause = " (title LIKE ? OR context LIKE ?)";
        $search_params = ["%" . $search_query . "%", "%" . $search_query . "%"];
    }
    
    // Build the combined WHERE clause
    $where_clauses = [];
    if ($is_logged_in) {
        $where_clauses[] = "user_id = ?";
    }
    if (!empty($search_where_clause)) {
        $where_clauses[] = $search_where_clause;
    }
    
    $combined_where_clause = "";
    if (!empty($where_clauses)) {
        $combined_where_clause = " WHERE " . implode(" AND ", $where_clauses);
    }
    
    // --- PAGINATION LOGIC ---
    $posts_per_page = 5;

    // Get the total number of posts that match the criteria
    $count_sql = "SELECT COUNT(*) FROM posts" . $combined_where_clause;
    $count_stmt = $conn->prepare($count_sql);

    // Bind parameters for the count query
    $count_param_index = 1;
    if ($is_logged_in) {
        $count_stmt->bindValue($count_param_index++, $user_id_param, PDO::PARAM_INT);
    }
    if (!empty($search_params)) {
        $count_stmt->bindValue($count_param_index++, $search_params[0]);
        $count_stmt->bindValue($count_param_index++, $search_params[1]);
    }
    $count_stmt->execute();

    $total_posts = $count_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $posts_per_page);

    $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) { $current_page = 1; }
    if ($current_page > $total_pages) { $current_page = $total_pages; }

    $offset = ($current_page - 1) * $posts_per_page;
    if ($offset < 0) { $offset = 0; }

    // --- Final Query to Fetch Posts ---
    $sql = "SELECT id, title, context, created_at FROM posts" . $combined_where_clause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    // This is the CRUCIAL fix: Explicitly binding parameters with correct types
    $param_index = 1;
    if ($is_logged_in) {
        $stmt->bindValue($param_index++, $user_id_param, PDO::PARAM_INT);
    }
    if (!empty($search_params)) {
        $stmt->bindValue($param_index++, $search_params[0]);
        $stmt->bindValue($param_index++, $search_params[1]);
    }
    $stmt->bindValue($param_index++, $posts_per_page, PDO::PARAM_INT);
    $stmt->bindValue($param_index, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $posts = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Simple Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="new-header">
            <div class="logo">My Simple Blog</div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="add_post.php">Create Post</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <main class="posts-container">
            <h2><?php echo $is_logged_in ? "My Posts" : "All Posts"; ?></h2>
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="search_query" placeholder="Search posts..." value="<?php echo htmlspecialchars($search_query); ?>" class="search-input">
                <button type="submit" class="search-button">Search</button>
            </form>

            <?php if (!empty($search_query)): ?>
                <p class="search-results-info">Showing <?php echo $total_posts; ?> results for: "<?php echo htmlspecialchars($search_query); ?>"</p>
            <?php endif; ?>

            <?php if ($posts): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p class="post-meta">Posted on: <?php echo date("F j, Y", strtotime($post['created_at'])); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($post['context'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts found. Be the first to add one!</p>
            <?php endif; ?>

            <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php
                        $link_params = 'page=' . $i;
                        if (!empty($search_query)) {
                            $link_params .= '&search_query=' . urlencode($search_query);
                        }
                        ?>
                        <a href="?<?php echo $link_params; ?>" class="page-link <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date("Y"); ?> My Simple Blog</p>
        </footer>
    </div>
</body>
</html>