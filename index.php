<?php
require __DIR__ . '/scripts/db.php';

$perPage = 24;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("
  SELECT id, db_file_name, created_at
  FROM uploads
  ORDER BY id DESC
  LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll();

$total = (int)$pdo->query("SELECT COUNT(*) FROM uploads")->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

function h($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Rate My Poop</title>

  <!-- Global styles -->
  <link rel="stylesheet" href="css/style.css" />
  <!-- Feed-specific styles -->
  <link rel="stylesheet" href="css/style-feed.css" />

  <script defer src="scripts/app.js"></script>
</head>

<body>
    <!-- Top bar -->
    <header class="topbar">
        <div class="topbar__inner container">
        <a class="brand" href="#" aria-label="ratemypoop.net home">
            <span class="brand__mark">💩</span>
            <span class="brand__text">ratemypoop<span class="brand__dot">.net</span></span>
        </a>

        <nav class="nav">
            <a href="#" class="nav__link is-active">Feed</a>
            <a href="#" class="nav__link">Top</a>
            <a href="#" class="nav__link">New</a>
            <a href="#" class="nav__link">About</a>
            <a href="upload.html">Upload REAL</a>
        </nav>

        <div class="topbar__actions">
            <button class="btn btn--ghost" id="btnSignIn" type="button">Sign in</button>
            <button class="btn btn--primary" id="btnUpload" type="button">Upload</button>
            <a href="upload.html">Upload REAL</a>
        </div>
        </div>
    </header>

    <!-- Main -->
    <main class="container main">
        <!-- Hero / call to action -->
        <section class="hero">
            <div class="hero__text">
                <h1>Upload. Rate. Comment.</h1>
                <p class="muted">
                Like an image feed… but with <em>unapologetic honesty</em>.
                (Skeleton build: no real uploads yet.)
                </p>

                <div class="hero__cta">
                <button class="btn btn--primary" type="button" id="ctaUpload">Upload a specimen</button>
                <button class="btn btn--ghost" type="button" id="ctaRandom">Random</button>
                </div>

                <div class="pillrow" aria-label="Quick filters">
                <button class="pill is-active" type="button" data-filter="hot">🔥 Hot</button>
                <button class="pill" type="button" data-filter="new">🆕 New</button>
                <button class="pill" type="button" data-filter="top">🏆 Top</button>
                <button class="pill" type="button" data-filter="controversial">🧨 Spicy</button>
                </div>
            </div>

            <aside class="hero__panel">
                <div class="panel">
                <h2 class="panel__title">Leaderboard</h2>
                <ol class="panel__list">
                    <li><span class="tag">#1</span> “The Forbidden Swirl” <span class="score">9.4</span></li>
                    <li><span class="tag">#2</span> “Corn's Return” <span class="score">9.1</span></li>
                    <li><span class="tag">#3</span> “The Soft Serve” <span class="score">8.8</span></li>
                </ol>
                <p class="panel__note muted">Fake data for now. You're welcome.</p>
                </div>
            </aside>
        </section>

        <!-- Feed toolbar -->
        <section class="toolbar">
            <div class="toolbar__left">
                <h2 class="section-title">Today's Feed</h2>
                <span class="muted small" id="postCount">12 posts</span>
            </div>

            <div class="toolbar__right">
                <label class="search">
                <span class="sr-only">Search</span>
                <input id="searchInput" type="search" placeholder="Search titles, tags, chaos…" />
                </label>

                <label class="select">
                <span class="sr-only">Sort</span>
                <select id="sortSelect">
                    <option value="hot">Sort: Hot</option>
                    <option value="new">Sort: New</option>
                    <option value="top">Sort: Top</option>
                    <option value="rating">Sort: Highest Rating</option>
                </select>
                </label>
            </div>
        </section>

        <!-- Feed grid -->
        <section class="grid" id="feedGrid" aria-label="Post feed">
            <!-- Post cards (static placeholders) -->
            <article class="card" data-title="The Forbidden Swirl" data-tags="hot swirl classic">
                <div class="card__media">
                <img src="https://placehold.co/900x600?text=Specimen+%231" alt="Placeholder image for Specimen #1" />
                <span class="badge">🔥 Hot</span>
                </div>

                <div class="card__body">
                <h3 class="card__title">The Forbidden Swirl</h3>
                <p class="card__meta muted">by <span class="user">anonymous-rat-17</span> • 2h ago</p>

                <div class="card__actions">
                    <div class="vote">
                    <button class="iconbtn" type="button" aria-label="Upvote">⬆️</button>
                    <span class="vote__count">421</span>
                    <button class="iconbtn" type="button" aria-label="Downvote">⬇️</button>
                    </div>

                    <div class="rating" aria-label="Rating">
                    <span class="rating__value">9.4</span>
                    <span class="muted small">/ 10</span>
                    </div>

                    <button class="btn btn--ghost btn--sm" type="button">💬 38</button>
                </div>

                <div class="chiprow">
                    <span class="chip">#classic</span>
                    <span class="chip">#swirl</span>
                    <span class="chip">#bold</span>
                </div>
                </div>
            </article>

            <article class="card" data-title="Corn's Return" data-tags="top corn meme">
                <div class="card__media">
                <img src="https://placehold.co/900x600?text=Specimen+%232" alt="Placeholder image for Specimen #2" />
                <span class="badge">🏆 Top</span>
                </div>

                <div class="card__body">
                <h3 class="card__title">Corn's Return</h3>
                <p class="card__meta muted">by <span class="user">kernel-king</span> • 6h ago</p>

                <div class="card__actions">
                    <div class="vote">
                    <button class="iconbtn" type="button" aria-label="Upvote">⬆️</button>
                    <span class="vote__count">1,102</span>
                    <button class="iconbtn" type="button" aria-label="Downvote">⬇️</button>
                    </div>

                    <div class="rating" aria-label="Rating">
                    <span class="rating__value">9.1</span>
                    <span class="muted small">/ 10</span>
                    </div>

                    <button class="btn btn--ghost btn--sm" type="button">💬 112</button>
                </div>

                <div class="chiprow">
                    <span class="chip">#corn</span>
                    <span class="chip">#meme</span>
                    <span class="chip">#legendary</span>
                </div>
                </div>
            </article>

            <article class="card" data-title="The Soft Serve" data-tags="new softserve risky">
                <div class="card__media">
                <img src="https://placehold.co/900x600?text=Specimen+%233" alt="Placeholder image for Specimen #3" />
                <span class="badge">🆕 New</span>
                </div>

                <div class="card__body">
                <h3 class="card__title">The Soft Serve</h3>
                <p class="card__meta muted">by <span class="user">soft-launch</span> • 15m ago</p>

                <div class="card__actions">
                    <div class="vote">
                    <button class="iconbtn" type="button" aria-label="Upvote">⬆️</button>
                    <span class="vote__count">88</span>
                    <button class="iconbtn" type="button" aria-label="Downvote">⬇️</button>
                    </div>

                    <div class="rating" aria-label="Rating">
                    <span class="rating__value">6.6</span>
                    <span class="muted small">/ 10</span>
                    </div>

                    <button class="btn btn--ghost btn--sm" type="button">💬 9</button>
                </div>

                <div class="chiprow">
                    <span class="chip">#risky</span>
                    <span class="chip">#soft</span>
                    <span class="chip">#brave</span>
                </div>
                </div>
            </article>

        <!-- Duplicate a few more cards quickly by copy/paste later -->
        </section>

        <h2> MOST RECENT UPLOADS </h2>

        <?php if (count($rows) === 0): ?>
            <p>No uploads yet. Be the hero this site needs. 🐀💩</p>
        <?php else: ?>
            <section class="grid">
                <?php foreach ($rows as $r): ?>
                <?php
                    $id = (int)$r['id'];
                    $imgUrl = 'media/uploads/' . rawurlencode($r['db_file_name']);
                ?>
                    <a class="card" href="<?= h($imgUrl) ?>" target="_blank" rel="noopener">
                        <img src="<?= h($imgUrl) ?>" alt="Upload #<?= $id ?>" loading="lazy" />
                        <div class="meta">
                            <span>#<?= $id ?></span>
                            <span><?= h($r['created_at']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>

        <div class="pager">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&larr; Prev</a>
            <?php else: ?>
            <span>&larr; Prev</span>
            <?php endif; ?>

            <span>Page <?= $page ?> / <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Next &rarr;</a>
            <?php else: ?>
            <span>Next &rarr;</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer__inner">
        <p class="muted small">
            © <span id="year"></span> ratemypoop.net • Built for laughs •
            <a href="#" class="footer__link">Rules</a> • <a href="#" class="footer__link">Privacy</a>
        </p>
        <p class="muted small">Reminder: this is a skeleton. Your backend is not crying yet.</p>
        </div>
    </footer>

    <!-- Modal (fake upload) -->
    <dialog class="modal" id="uploadModal" aria-label="Upload modal">
        <form method="dialog" class="modal__content">
        <div class="modal__header">
            <h2>Upload a specimen</h2>
            <button class="iconbtn" value="cancel" aria-label="Close">✖️</button>
        </div>

        <div class="modal__body">
            <p class="muted">No real uploads yet. This is just UI scaffolding.</p>

            <label class="field">
            <span class="field__label">Title</span>
            <input type="text" placeholder="e.g., The Midnight Nugget" />
            </label>

            <label class="field">
            <span class="field__label">Tags</span>
            <input type="text" placeholder="e.g., #bold #mystery #corn" />
            </label>

            <div class="dropzone">
            <div class="dropzone__inner">
                <p><strong>Drop an image here</strong> (later)</p>
                <p class="muted small">For now: pretend this is a file input.</p>
            </div>
            </div>
        </div>

        <div class="modal__footer">
            <button class="btn btn--ghost" value="cancel" type="submit">Cancel</button>
            <button class="btn btn--primary" value="default" type="submit">Looks good</button>
        </div>
        </form>
    </dialog>

</body>
</html>
