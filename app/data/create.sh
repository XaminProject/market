#!/bin/bash

sort -u ./messages.php > ./messages1.php
mv ./messages1.php ./messages.php 
echo "<?php" > ../messages.php
cat ./messages.php >> ../messages.php
chmod 777 ./messages.php

