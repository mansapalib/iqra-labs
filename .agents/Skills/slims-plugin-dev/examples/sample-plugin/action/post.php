<?php
/**
 * Action scripts fired upon plugin activation / installation
 */

class Post
{
    public function install()
    {
        // Jalankan perintah post-install (misal membuat direktori uploads atau cache plugin)
        $cache_dir = __DIR__ . '/../cache';
        if (!is_dir($cache_dir)) {
            @mkdir($cache_dir, 0755, true);
        }
    }

    public function uninstall()
    {
        // Pembersihan berkas sementara saat uninstall jika diperlukan
    }
}
