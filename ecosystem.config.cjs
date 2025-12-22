module.exports = {
    apps: [
        {
            name: process.env.PM2_APP_NAME || 'gerenciador-oticas-api',
            script: 'artisan',
            interpreter: 'php',
            // Use 127.0.0.1 and a non-standard port to be proxied by Apache
            args: 'serve --host=127.0.0.1 --port=' + (process.env.PORT || 10002),
            // Keep the app where it already lives on the droplet
            cwd: process.env.APP_CWD || '/home/documents/mvl/gerenciador-de-oticas-api',
            instances: 1,
            exec_mode: 'fork',
            env: {
                NODE_ENV: 'production',
                APP_ENV: 'production',
                APP_KEY: process.env.APP_KEY,
                DB_CONNECTION: process.env.DB_CONNECTION,
                DB_HOST: process.env.DB_HOST,
                DB_PORT: process.env.DB_PORT,
                DB_DATABASE: process.env.DB_DATABASE,
                DB_USERNAME: process.env.DB_USERNAME,
                DB_PASSWORD: process.env.DB_PASSWORD,
                MAIL_MAILER: process.env.MAIL_MAILER,
                MAIL_HOST: process.env.MAIL_HOST,
                MAIL_PORT: process.env.MAIL_PORT,
                MAIL_USERNAME: process.env.MAIL_USERNAME,
                MAIL_PASSWORD: process.env.MAIL_PASSWORD,
                MAIL_ENCRYPTION: process.env.MAIL_ENCRYPTION,
                MAIL_FROM_ADDRESS: process.env.MAIL_FROM_ADDRESS,
                MAIL_FROM_NAME: process.env.MAIL_FROM_NAME,
            },
            error_file: process.env.PM2_ERROR_LOG || '/home/documents/mvl/gerenciador-de-oticas-api/logs/error.log',
            out_file: process.env.PM2_OUT_LOG || '/home/documents/mvl/gerenciador-de-oticas-api/logs/out.log',
            log_file: process.env.PM2_COMBINED_LOG || '/home/documents/mvl/gerenciador-de-oticas-api/logs/combined.log',
            time: true,
            watch: false,
            max_memory_restart: '1G',
            restart_delay: 4000,
            autorestart: true,
        }
    ]
};
