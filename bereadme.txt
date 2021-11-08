
This version is Homestead / Vagrant

To run locally:

    http://pos_utilities.test

Use GIT

    Use SSH authentication
    Setting the remore to use SSH
    git remote set-url origin git@github.com:bee7er/pos_utilities.git

Mysql:

    use the vagrant ssh command line:
	
		mysql -uroot -psecret

	CREATE DATABASE pos_utilities;

	GRANT ALL ON pos_utilities.* TO brian@'localhost' IDENTIFIED BY 'Cantata625';

	GRANT ALL ON brianeth_pos.* TO brian@'localhost' IDENTIFIED BY 'Cantata625';

# POS

1. Import latest price PDF file into database
2. Form to input product codes
3. Save these as a list
4. Show them as a comma separated list of PDF page numbers

Webpack

    For packaging up the js and css resources in Laravel

    Look at webpack.mix.js

    We run the compilation of these with node:

        npm -v      # to see which version is installed

        # Run install of all the dependencies defined in package.json
        # Note that this adds thousands of files to the project
        # To stop these from being indexed go to PhpStorm | Preferences | Project | Directories and exclude node_modules

        npm install

        # To run the compile, as shown in the package.json file, run:

        npm run development

        # or

        npm run dev