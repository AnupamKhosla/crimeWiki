# crimeWiki.in
## Full stack php cms based wikipedea scraping project designed and developed solely by Anupam Khosla https://www.linkedin.com/in/anupamkhosla/

Official site: http://crimewiki.in 
Admin Panel looks like :  //images

Wikipedea of major criminals, criminal organizations and crime evets. cms based admin panel, which is capable of scraping 500 wikipedea pages in a few seconds.

------------------

### How to install on your server or local machine

**Step 1:** Download this git repository. Note your server's databse name, username and password. Open `login.php` in your browser. Use the same database details. Fill in any username and password for cms login.  

**Step 2:** 

- Go to yourdomain/categories.php page first and create a category named `criminals`.  Homepage will show `criminals` category by default.  
- Add a minimum of one post through yourdomain/wikipedea.php or yourdomain/addpost.php. 
- Go to yourdomain/dashboard.php and copy `title` of that post.
- Paste the title into `Crime of the month post`.
- Set the `About The CrimeWiki text`. 

Go to yourdomain and the website will work now.

**Meta:** php will automatically create category named `blog` -- this is mandatory for homepage to show dynamic posts and about us section text. php will make two posts in the blog category, namely `$blog_month_post` and `$blog_about_text`. These two will be used to store about us data and monthly-post data.

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

**Bugs**  

- Page: http://crimewiki.in/search?title=rangh&category=&filter=  See details link problem. quotes not converted into html entity.  

- Advance seach not working properly with single letters or only-numbers e.g.:  `o` and `17`

http://crimewiki.in/search?advance=on&title=17&category=&filter=  
http://crimewiki.in/search?advance=on&title=o&category=&filter=

17 and 43 come in some pages content which are not shown in results.