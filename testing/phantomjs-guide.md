EgyptFOSS
====

PhantomJS 2.1.1 Installation Guide
=
***************************************************************************

First, install or update to the latest system software.
```bash
	sudo apt-get update
	sudo apt-get install build-essential chrpath libssl-dev libxft-dev
```	
Install these packages needed by PhantomJS to work correctly.
```bash
	sudo apt-get install libfreetype6 libfreetype6-dev
	sudo apt-get install libfontconfig1 libfontconfig1-dev
```

Install these packages needed by PhantomJS to work correctly.
```bash
	sudo apt-get install libfreetype6 libfreetype6-dev
	sudo apt-get install libfontconfig1 libfontconfig1-dev
```

Install these packages 
```bash
sudo apt-get install autoconf2.13 pkg-config build-essential qt5-qmake g++ python ruby perl sqlite flex bison gperf openssl fontconfig xorg xorg-dev xutils-dev xcb-proto libtool libsqlite0 libssl-dev libsqlite3-dev libfontconfig1-dev libicu-dev libfreetype6 libssl-dev libpng-dev libpng12-dev libjpeg-dev libx11-dev libxext-dev libxcb-xkb-dev x11proto-core-dev libxcb-render-util0 libqt5webkit5-dev
```

Get it from the [PhantomJS website](http://phantomjs.org/).
```bash
	cd ~
	export PHANTOM_JS="phantomjs-2.1.1-linux-x86_64"
	wget https://bitbucket.org/ariya/phantomjs/downloads/$PHANTOM_JS.tar.bz2
	sudo tar xvjf $PHANTOM_JS.tar.bz2
```

Once downloaded, move Phantomjs folder to `/usr/local/share/` and create a symlink:
```bash
	sudo mv $PHANTOM_JS /usr/local/share
	sudo ln -sf /usr/local/share/$PHANTOM_JS/bin/phantomjs /usr/local/bin
```

Now, It should have PhantomJS properly on your system.
```bash
	phantomjs --version
```