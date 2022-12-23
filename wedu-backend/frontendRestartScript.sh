cd /var/www/html/wedufront
sudo rm -rf .next
sudo npm run build
pm2 kill
pm2 start npm --name "wedufront" -- start
