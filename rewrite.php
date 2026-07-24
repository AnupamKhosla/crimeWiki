<?php 
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/check_login.php");

$conn = make_db_connection();

$has_cleansed_col = $conn->query("SHOW COLUMNS FROM posts LIKE 'cleansed'");
if ($has_cleansed_col->num_rows === 0) {
    $conn->query("ALTER TABLE posts ADD COLUMN cleansed TINYINT DEFAULT 0");
}

if (!empty($_POST["set_api_key"])) {
    $_SESSION["qwen_api_key"] = trim($_POST["api_key"]);
}

if (!empty($_POST["save_post"])) {
    $save_id = (int)$_POST["save_post_id"];
    $save_content = $_POST["save_content"];
    $stmt = $conn->prepare("UPDATE posts SET content = ?, cleansed = 1 WHERE id = ?");
    $stmt->bind_param("si", $save_content, $save_id);
    $stmt->execute();
    $stmt->close();
}

$batch = [];
$result = $conn->query("SELECT id, title, cleansed FROM posts WHERE cleansed = 0 ORDER BY id LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $batch[] = $row;
    }
}

$total_stmt = $conn->query("SELECT COUNT(*) as total FROM posts");
$total = $total_stmt->fetch_assoc()["total"];
$cleansed_stmt = $conn->query("SELECT COUNT(*) as done FROM posts WHERE cleansed = 1");
$done = $cleansed_stmt->fetch_assoc()["done"];

$has_key = !empty($_SESSION["qwen_api_key"]);
?>

<!doctype html>
<html class="no-js dashboard" lang="">
<head>
  <meta charset="utf-8">
  <title>Rewrite | CrimeWiki Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="assets/img/logo_single.svg">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="assets/css/admin.css">
  <style>
    .rewrite-card { background: var(--white-bg); border-radius: 5px; padding: 20px; margin-bottom: 15px; border-left: 4px solid var(--theme-pm); }
    .rewrite-card.done { border-left-color: #28a745; opacity: 0.6; }
    .rewrite-card .status-badge { font-size: 0.75rem; padding: 2px 8px; border-radius: 3px; }
    .rewrite-card .status-pending { background: #ffc107; color: #333; }
    .rewrite-card .status-done { background: #28a745; color: #fff; }
    .preview-box { max-height: 400px; overflow-y: auto; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; font-size: 0.85rem; white-space: pre-wrap; word-break: break-word; margin-top: 10px; }
    .btn-rewrite { background: var(--theme-pm); color: #fff; border: none; padding: 8px 20px; border-radius: 4px; }
    .btn-rewrite:disabled { opacity: 0.5; cursor: not-allowed; }
    .progress-info { font-size: 0.875rem; color: #666; }
    .spinner { display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <?php require_once("include/sidebar_dashboard.php"); ?>

      <div class="col-lg-10 content d-flex justify-content-between flex-column">
        <main class="pb-5">
          <h1 class="text-center text-pm font-weight-lighter h4">Content Rewrite (AI Cleansing)</h1>
          <hr>

          <div class="row mb-3">
            <div class="col-md-6">
              <p class="progress-info">Progress: <strong><?php echo $done; ?> / <?php echo $total; ?></strong> posts cleansed</p>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-success" style="width: <?php echo $total ? round($done/$total*100) : 0; ?>%"></div>
              </div>
            </div>
            <div class="col-md-6">
              <form method="post" class="form-inline justify-content-md-end">
                <label class="mr-2 font-weight-normal" for="api_key">Qwen API Key:</label>
                <input type="password" name="api_key" id="api_key" class="form-control form-control-sm mr-2" style="width: 250px;" value="<?php echo $has_key ? "••••••••" : ""; ?>" placeholder="sk-..." <?php echo $has_key ? "disabled" : ""; ?>>
                <?php if (!$has_key): ?>
                  <button type="submit" name="set_api_key" value="1" class="btn btn-sm btn-rewrite">Set Key</button>
                <?php else: ?>
                  <span class="badge badge-success">Key Set</span>
                <?php endif; ?>
              </form>
            </div>
          </div>

          <?php if (!$has_key): ?>
            <div class="alert alert-warning">Set your Qwen API key above to enable rewriting. The key is stored in your session only — never saved to DB or files.</div>
          <?php endif; ?>

          <h2 class="h5 text-pm text-center my-4">Next Batch (Uncleansed Posts)</h2>

          <?php if (empty($batch)): ?>
            <div class="alert alert-success text-center">All posts have been cleansed. Nothing left to rewrite.</div>
          <?php else: ?>
            <div id="batch-container">
              <?php foreach ($batch as $i => $post): ?>
                <div class="rewrite-card" id="card-<?php echo $post["id"]; ?>">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <strong>#<?php echo $post["id"]; ?></strong> — <?php echo htmlspecialchars($post["title"]); ?>
                    </div>
                    <span class="status-badge status-pending" id="badge-<?php echo $post["id"]; ?>">Pending</span>
                  </div>
                  <div class="mt-2">
                    <button class="btn btn-sm btn-rewrite" id="btn-<?php echo $post["id"]; ?>" onclick="rewritePost(<?php echo $post["id"]; ?>)" <?php echo !$has_key ? "disabled" : ""; ?>>
                      Rewrite
                    </button>
                    <button class="btn btn-sm btn-success d-none" id="save-<?php echo $post["id"]; ?>" onclick="savePost(<?php echo $post["id"]; ?>)">
                      Approve &amp; Save
                    </button>
                    <button class="btn btn-sm btn-secondary d-none" id="reject-<?php echo $post["id"]; ?>" onclick="rejectPost(<?php echo $post["id"]; ?>)">
                      Reject
                    </button>
                  </div>
                  <div class="preview-box d-none" id="preview-<?php echo $post["id"]; ?>"></div>
                  <form method="post" id="form-<?php echo $post["id"]; ?>" class="d-none">
                    <input type="hidden" name="save_post_id" value="<?php echo $post["id"]; ?>">
                    <input type="hidden" name="save_content" id="content-<?php echo $post["id"]; ?>">
                    <input type="hidden" name="save_post" value="1">
                  </form>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="text-center mt-3">
              <button class="btn btn-rewrite px-5" id="rewrite-all" onclick="rewriteAll()" <?php echo !$has_key ? "disabled" : ""; ?>>
                Rewrite Entire Batch
              </button>
            </div>
          <?php endif; ?>

        </main>
        <?php require_once("include/footer_dashboard.php") ?>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.bundle.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    const postIds = [<?php echo implode(",", array_column($batch, "id")); ?>];

    function rewritePost(id) {
      const btn = document.getElementById("btn-" + id);
      const preview = document.getElementById("preview-" + id);
      const badge = document.getElementById("badge-" + id);

      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Rewriting...';
      preview.classList.remove("d-none");
      preview.textContent = "Contacting Qwen AI... (this may take 30-90 seconds)";

      fetch("include/rewrite_api.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "post_id=" + id
      })
      .then(r => r.json())
      .then(data => {
        if (data.error) {
          preview.textContent = "ERROR: " + data.error;
          btn.disabled = false;
          btn.textContent = "Retry";
          return;
        }
        preview.textContent = data.new_content;
        document.getElementById("content-" + id).value = data.new_content;
        document.getElementById("save-" + id).classList.remove("d-none");
        document.getElementById("reject-" + id).classList.remove("d-none");
        btn.classList.add("d-none");
        badge.textContent = "Preview Ready";
        badge.className = "status-badge status-pending";
      })
      .catch(err => {
        preview.textContent = "Network error: " + err.message;
        btn.disabled = false;
        btn.textContent = "Retry";
      });
    }

    function rewriteAll() {
      const allBtn = document.getElementById("rewrite-all");
      allBtn.disabled = true;
      allBtn.innerHTML = '<span class="spinner"></span> Processing batch...';
      let i = 0;
      function next() {
        if (i >= postIds.length) {
          allBtn.textContent = "Batch Complete";
          return;
        }
        const id = postIds[i];
        const btn = document.getElementById("btn-" + id);
        if (btn && !btn.classList.contains("d-none")) {
          rewritePost(id);
          setTimeout(() => { i++; next(); }, 2000);
        } else {
          i++;
          next();
        }
      }
      next();
    }

    function savePost(id) {
      document.getElementById("form-" + id).submit();
    }

    function rejectPost(id) {
      document.getElementById("preview-" + id).classList.add("d-none");
      document.getElementById("save-" + id).classList.add("d-none");
      document.getElementById("reject-" + id).classList.add("d-none");
      const btn = document.getElementById("btn-" + id);
      btn.classList.remove("d-none");
      btn.disabled = false;
      btn.textContent = "Retry";
      document.getElementById("badge-" + id).textContent = "Rejected";
    }
  </script>
</body>
</html>
