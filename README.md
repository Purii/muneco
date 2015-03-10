# muneco - A WP-Plugin to support you managing a multilingual WordPress instance using a Network
[![Build Status](https://travis-ci.org/Purii/muneco.svg?branch=master)](https://travis-ci.org/Purii/muneco)

## Project
The Plugin is still under development and not ready for production. The development is really time consuming, so I wanted to share the current state and invite you to do some pull requests. It would be also great if there will be some useful feedback!

## Description
muneco supports you managing a multilingual WordPress instance using a Network. For that muneco extends your WP-Network without manipulating the whole behaviour of the system. It can get activated or deactivated on the fly without destroying anything.

## Features
Discover what MuNeCo can currently do for you:

* Connect Posts, Pages and Custom Post Types(in development) across the network
* Defining different language codes for Back- & Frontend
* Multilingual Sitemap
* Alternate hreflang annotations
* Recognize transitive connections


The modular architecture is a fantastic feature of muneco. Making it most flexible on doing it's job and gives you real control.
Every developer is invited contributing new modules or extending current ones. Have a look at the Overview module to get inspired by the architecture.

### Currently available Modules

*	Sitemap
Provides a multilingual XML-Sitemap (/sitemap.xml) - ready for Google

*	Overview
Provides an overview of your Connections

### Currently planned Modules

* Support for MO-Files to translate static text

## Installation

### Prerequisites
* You need a running WordPress Network

### Installation from the repository
**You need GRUNT to build the zip-file**   
1.  Download the whole project   
2.  Run *grunt dist*  
2.  Install plugin through the Network-Admin by uploading the resulting zip of /dist   
3.  Activate through Network-Admin   

### Installation from repository - uncompressed files
1. Download the whole project   
2. Install the plugin through the Network-Admin by uploading /src to /wp-content/plugins/muneco   
3. Activate through Network-Admin   
