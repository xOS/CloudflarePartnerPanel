# Cloudflare CNAME Setup

[![GitHub stars](https://img.shields.io/github/stars/xOS/CloudflarePartnerPanel?label=github+stars)](https://github.com/xOS/CloudflarePartnerPanel)
[![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/xOS/CloudflarePartnerPanel)](https://github.com/xOS/CloudflarePartnerPanel/releases/latest)

This project allows [Cloudflare Hosting Partner][1] to provide a panel for customers, which allows customers to have [CNAME setup][2] for **free**.

## Installation

If you don’t want to use the preinstalled panel above, you can install this panel on your server. [For more information, please see the Wiki][6].

## Features of this panel

+ Manage all your DNS records in one place. Using the [Cloudflare API v4][7], this project supports various types of DNS records.
+ <del>高级统计. You can see the analytics report for a whole **previous year**, rather than a month.</del>
+ Supports NS setup. This panel provides NS setup information so you can switch to Cloudflare DNS at any time. You can also find your DNSSEC feature is also supported.
+ Supports IP setup. This service provides the anycast IPv4 and IPv6 of the CDN. You can safely use this service on the root domain.
+ Supports mobile devices.
+ Supports multi-languages.

## How to switch to this panel from NS setup

1. Backup your existing record
2. Switch your domain to another DNS services and restore the DNS record (Optional)
3. Delete your domain on Cloudflare (Might have downtime if you have not done step 2)
4. Re-add your domain on the panel
5. Setup the DNS records on this panel
6. Delete the existing DNS record and CNAME to Cloudflare (Only if you have done step 2)

## Advantages for CNAME setup

+ You can use any DNS provider you like, which is much more flexible.
+ Use Cloudflare CDN as a backup server or use multiple CDNs.
+ Support fourth-level subdomain SSL for free! For example, the domain like `dev.project.example.com` is ready for HTTPS. This is because, for CNAME setup, the Cloudflare issues the [SSL for SaaS][8], which is an SSL separately for each sub-domain immediately. 

## Advantages when you use Cloudflare

**You don’t need to install any software on your origin**. Just configure your origin server information on the panel, delete the existing DNS record and CNAME to Cloudflare or switch to Cloudflare DNS, and you are done!

+ Unmetered Mitigation of DDoS
+ Global CDN. Your website will be much faster.
+ I’m Under Attack™ mode
+ Always Online ™
+ Page Rules included. You can customize the cache behavior, set up 301/302 redirect, and much more.

## Screenshot

<img alt="Screenshoot 1" src="https://user-images.githubusercontent.com/6601455/112777852-c5db7780-9075-11eb-9c72-4e5061b66b6d.png" />
<img alt="Screenshoot 2" src="https://user-images.githubusercontent.com/6601455/112777867-cd9b1c00-9075-11eb-9b34-f168175aa923.png" width="433" />

## Open-sourced software used in this project

This project was based on a [HOSTLOC topic][9].

+ jQuery | MIT License
+ popper.js | MIT License
+ Bootstrap | MIT License
+ Chart.js by Nick Downie | MIT License
+ Guzzle by Michael Dowling [mtdowling@gmail.com][10] | MIT License
+ PSR Http Message by Framework Interoperability Group | MIT License
+ Composer | MIT License
+ Net\_DNS2 by Mike Pultz [mike@mikepultz.com][11] | BSD-3-Clause License
+ Cloudflare SDK by Cloudflare | BSD-3-Clause

[1]:    https://www.cloudflare.com/partners/hosting-provider/
[2]:    https://support.cloudflare.com/hc/en-us/articles/200168706-How-do-I-do-CNAME-setup-
[3]:    https://github.com/ZE3kr/Cloudflare-CNAME-Setup/blob/master/README.zh.md
[4]:    https://cf.tlo.xyz
[6]:    https://github.com/ZE3kr/Cloudflare-CNAME-Setup/wiki/Installation
[7]:    https://api.cloudflare.com/
[8]:    https://www.cloudflare.com/ssl-for-saas-providers/
[9]:    http://www.hostloc.com/thread-386441-1-1.html
[10]:    mailto:mtdowling@gmail.com
[11]:    mailto:mike@mikepultz.com
