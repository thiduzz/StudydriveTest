# StudydriveTest - Login/Register Task

This small application was developed as a demonstration for Studydrive.net and it covers the following scope:

- Simultaneous Register/Login (with automatic creation on failed login and the suggested validation)
- Account Confirmation
- Password Recover
- Access control with middleware
- Throttling of logins and activation email resend
- Logout
- Gravatar integrated
- Amazon S3 as a repository for uploaded avatar images

#Technologies used

- Laravel 5.3
- VueJS 2.0
- MySQL Database
- Yarn, Bower and NPM (Dependency Managers)
- Amazon AWS (S3)
- Gravatar
- Gulp (Laravel Elixir and Webpack)
- .env (for environment variable management)
- Vagrant 1.8.4 (with the Homestead box)
- Virtual Box 5.0.24

#Installation Instructions
- Before anything you will need to install Vagrant (with Homestead), VirtualBox, NPM, Bower, Yarn and Gulp
- On your Homestead directory create a folder called "sites" 
- Clone this repository into a folder called "studydrive" inside the "sites" folder
- On a terminal run the command "composer install" on the root folder of this project
- On a terminal run the command "yarn" on the root folder of this project (this should install all the NPM packages)
- On a terminal run the command "bower install" on the root folder of this project
- Add the following lines in your Homestead.yaml

```html
ip: "192.168.10.10"
memory: 2048
cpus: 1
provider: virtualbox

authorize: C:/Users/YourUser/.ssh/id_rsa.pub

keys:
    - C:/Users/YourUser/.ssh/id_rsa

folders:
    - map: C:/Users/YourUser/HomesteadRootDirectory/sites
      to: /home/vagrant/sites

sites:
    - map: studydrive.app
      to: /home/vagrant/sites/studydrive/public
      
databases:
    - studydrive

variables:
    - key: APP_ENV
      value: local
```

- On a Win32 system add the following line to your hosts file (usually on C:\Windows\System32\drivers\etc directory) and save

```html
192.168.10.10 studydrive.app
```
- CD into your Homestead directory and run the command "vagrant up" to run the server
- Once the server is up, run "vagrant ssh" and log with your SSH access
- Change the .env according to your credentials
- CD into the sites/studydrive and run the command "php artisan migrate" to migrate the database schema
- You are ready to go, access the application on: http://studydrive.app
