raffle_app_v5 - Instrucciones rápidas
------------------------------------
1) Edita config.php y coloca DB_USER y DB_PASS con tus credenciales MySQL.
2) Sube la carpeta al servidor web (por ejemplo: /public_html/raffle_app_v5/).
3) Importa sql_init.sql en tu servidor MySQL (phpMyAdmin o CLI):
   mysql -u user -p < sql_init.sql
4) Accede a /register.php para crear cuentas o usa el admin inicial:
   email: admin@example.com
   password: Admin123!
5) Desde el panel admin -> 'Actualizar tasa' puedes cambiar la tasa USD→Bs.
6) Los usuarios pueden reportar pagos con métodos: zelle, binance, movil, transfer.
7) Admin puede verificar/rechazar pagos y asignar tickets.

NOTA: Reemplaza las constantes DB_USER/DB_PASS en config.php antes de usar.
