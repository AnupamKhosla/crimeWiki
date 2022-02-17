## crimeWiki.in
### Full stack php cms based wikipedea scraping project designed and developed solely by Anupam Khosla 

Wikipedea of major criminals, criminal organizations and crime events. CMS based admin panel, which is capable of scraping 500 wikipedea pages in a few seconds.

- Official site: http://crimewiki.in 
- Find me at https://www.linkedin.com/in/anupamkhosla/

Admin Panel looks like :  

- Login page: https://anupamkhosla.github.io/crimeWiki/assets/img/login.png  
- Dashboard page: https://anupamkhosla.github.io/crimeWiki/assets/img/dashboard.png  
- Posts search: https://anupamkhosla.github.io/crimeWiki/assets/img/posts.png  
- Addpost page: https://anupamkhosla.github.io/crimeWiki/assets/img/addpost.png  
- Categories page: https://anupamkhosla.github.io/crimeWiki/assets/img/categories.png 
- Wikipedea page: https://anupamkhosla.github.io/crimeWiki/assets/img/wikipedea.png


------------------

#### How to install on your server or local machine

**Step 1:** Download this git repository. Note your server's databse name, username and password. Open `login.php` in your browser. Use the same database details. Fill in any username and password for cms login.  

**Step 2:** 

- Go to yourdomain/categories.php page first and create a category named `Criminals`.  Homepage will show `Criminals` category by default.  
- Add a minimum of one post through yourdomain/wikipedea.php or yourdomain/addpost.php. 
- Go to yourdomain/dashboard.php and copy `title` of that post.
- Paste the title into `Crime of the month post`.
- Set the `About The CrimeWiki text`. 

Go to yourdomain and the website will work now.

**Meta:** php will automatically create category named `Blog` -- this is mandatory for homepage to show dynamic posts and about us section text. php will make two posts in the blog category, namely `$blog_month_post` and `$blog_about_text`. These two will be used to store about us data and monthly-post data.

htaccess rewrites being used:  

```
<IfModule mod_rewrite.c>
    Options -MultiViews
    RewriteEngine On
    RewriteRule ^sitemap/sitemap-index.xml sitemap/sitemap-index.php
    RewriteRule ^sitemap/sitemap(\d+).txt sitemap/sitemap.php?page=$1
    RewriteRule ^post/(\d+$) post.php?id=$1 
    RewriteRule ^post/([^/]+)/(\d+) post.php?title=$1&repeat=$2 
    RewriteRule ^post/([^/]*) post.php?title=$1 
 </IfModule>
  
 <IfModule mod_rewrite.c>
   # RewriteEngine On
   # RewriteRule ^post/(\d+(/|$)).* post.php?id=$1
   # RewriteRule ^post/(?!\d+($|/))([^/\n\r]+)($|/)(\d+)? post.php?title=$2&repeat=$4 
   # Very important regexes created for post.php page
 </IfModule>

```

-------------------

