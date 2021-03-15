1. Install a webserver package with HTTP + PHP + MySQL (f.e. laragon or xampp).
2. Start webservers HTTP + MySQL.
3. Execute SQL scripts on MySQL database: first `webshop_structure.sql`, then `webshop_data.sql`.
4. Copy `www` folder contents into HTTP documents folder (`www` on laragon/nginx, `htdocs` on xampp/apache).
5. You can reach the webshop application with your browser by address `localhost` or `localhost:8080` (port requirement depends on server configuration).
