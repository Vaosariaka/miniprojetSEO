<section style="width:100%;max-width:460px;padding:1.5rem;background:#fff;border:1px solid #ddd;border-radius:8px">
    <h2 style="margin:0 0 1rem 0">Connexion BackOffice</h2>

    <?php if (!empty($loginError)): ?>
        <p style="color:#b00020;margin:0 0 1rem 0"><?= esc($loginError) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= esc(($base ?: '') . '/login') ?>">
        <label for="username" style="display:block;margin-bottom:.4rem">Utilisateur</label>
        <input
            id="username"
            name="username"
            type="text"
            value="<?= esc($loginUsername ?? 'admin') ?>"
            required
            style="width:100%;padding:.6rem;margin-bottom:1rem"
        >

        <label for="password" style="display:block;margin-bottom:.4rem">Mot de passe</label>
        <input
            id="password"
            name="password"
            type="password"
            value="<?= esc($loginPassword ?? 'admin123') ?>"
            required
            style="width:100%;padding:.6rem;margin-bottom:1rem"
        >

        <button type="submit" style="background:#222;color:#fff;border:none;padding:.6rem 1rem;cursor:pointer">Se connecter</button>
    </form>
</section>
