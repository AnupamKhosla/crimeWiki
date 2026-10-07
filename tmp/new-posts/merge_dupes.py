#!/usr/bin/env python3
"""merge_dupes.py dry | apply | verify      (always run from the project root)
Owner's rule (7 October 2026): posts with the same title about the same subject are merged;
a post on a different subject gets its own title.
Merge = the kept post takes the title alone (titlerepeat NULL, the clean URL); the other copies
are deleted. Their old URLs (/post/<slug>/<n>, /post/<id>) then show the site's normal 404 (owner's choice).
Kept copy: today's checked rewrite where there is one, else the copy with less Wikipedia wording
(tmp/legacy-wiki-audit/results.jsonl), and for Lawrence Bishnoi the owner's own post 1104.
dry     runs everything on the LIVE database inside a transaction and rolls it back.
apply   backs up the posts table on the VPS, saves the deleted rows locally (restore.sql), commits.
verify  opens each kept post's clean URL and each removed copy's old URL."""
import sys,os,json,subprocess,urllib.parse,re
BASE=os.path.dirname(os.path.abspath(__file__)); D=f'{BASE}/update/merge-20261007'
SSH=['gcloud','compute','ssh','crimewiki','--zone','us-east1-c','--command']
# title: (kept id, [deleted ids])
MERGE={
 '10 August 2015 Kabul suicide bombing':(679,[638]),
 '14K (triad)':(279,[288]),
 '1987 Lieyu massacre':(959,[958]),
 '2008 bombing of Indian embassy in Kabul':(677,[617]),
 '2009 bombing of Indian embassy in Kabul':(678,[621]),
 '22 August 2015 Kabul suicide bombing':(680,[639]),
 'Brothers Hospitallers of Saint John of God':(374,[373]),
 'David and Catherine Birnie':(475,[476]),
 'Diablos Motorcycle Club':(1054,[298]),
 'Mongols Motorcycle Club':(251,[1072]),
 'Murder of Anita Cobby':(471,[470]),
 'Nomads Motorcycle Club (Australia)':(1073,[335]),
 'Hells Angels':(330,[247,1062]),
 'Peckerwood':(1079,[254]),
 'Highway 61 Motorcycle Club':(1064,[332]),
 'Finks Motorcycle Club':(1056,[329]),
 'Lawrence Bishnoi':(1104,[1105]),
}
RENAME={380:('Peter Scully',"Daisy's Destruction")}  # post 380 is about the video, not the man
def hx(s): return "CONVERT(X'"+s.encode('utf-8').hex()+"' USING utf8mb4) COLLATE utf8mb4_unicode_ci"
def ids(): return sorted(i for k,(_,d) in MERGE.items() for i in d)

def script():
    dele=','.join(map(str,ids()))
    keep=','.join(str(k) for k,_ in MERGE.values())
    sql=["SET NAMES utf8mb4;","START TRANSACTION;",
         f"SELECT 'OLD', id, title, IFNULL(titlerepeat,'NULL') FROM posts WHERE id IN ({dele},{keep},{','.join(map(str,RENAME))}) ORDER BY id;"]
    for t,(k,dd) in MERGE.items():
        for i in dd: sql.append(f"DELETE FROM posts WHERE id = {i} AND title = {hx(t)};")
        sql.append(f"UPDATE posts SET titlerepeat = NULL WHERE id = {k} AND title = {hx(t)};")
    for i,(old,new) in RENAME.items():
        sql.append(f"UPDATE posts SET title = {hx(new)}, titlerepeat = NULL WHERE id = {i} AND title = {hx(old)};")
    titles=','.join(hx(t) for t in list(MERGE)+[n for _,n in RENAME.values()]+[o for o,_ in RENAME.values()])
    sql.append(f"SELECT 'NEW', title, COUNT(*), GROUP_CONCAT(id), GROUP_CONCAT(IFNULL(titlerepeat,'NULL')) FROM posts WHERE title IN ({titles}) GROUP BY title ORDER BY title;")
    return f'''set -u
MODE="${{1:-dry}}"
DB=$(sudo docker ps --format "{{{{.Names}}}}" </dev/null | grep -i -E "db|mysql" | grep -v -i phpmyadmin | head -1)
[ -n "$DB" ] || {{ echo "ERROR no database container"; exit 1; }}
if [ "$MODE" = "apply" ]; then
  mkdir -p "$HOME/crimewiki-backups"
  B="$HOME/crimewiki-backups/posts-before-merge-20261007.sql.gz"
  sudo docker exec "$DB" sh -c 'mysqldump --skip-lock-tables --no-tablespaces --default-character-set=utf8mb4 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" posts 2>/dev/null' </dev/null | gzip -c > "$B"
  SIZE=$(stat -c %s "$B"); echo "BACKUP $B $SIZE"
  [ "$SIZE" -gt 1000000 ] || {{ rm -f "$B"; echo "ERROR backup too small; nothing was changed"; exit 1; }}
  echo "RESTORE-BEGIN"
  sudo docker exec "$DB" sh -c 'mysqldump --skip-lock-tables --no-tablespaces --no-create-info --skip-extended-insert --default-character-set=utf8mb4 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" posts --where="id IN ({dele})" 2>/dev/null' </dev/null | grep "^INSERT"
  echo "RESTORE-END"
  END="COMMIT;"
else
  END="ROLLBACK;"
fi
{{ cat <<'SQLEOF'
{chr(10).join(sql)}
SQLEOF
echo "$END"
}} | sudo docker exec -i "$DB" sh -c 'mysql --default-character-set=utf8mb4 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -B -N 2>&1' | grep -v "Using a password"
echo "MODE $MODE DONE"
'''

def run(mode):
    os.makedirs(D,exist_ok=True)
    if mode=='apply':
        if not os.path.exists(f'{D}/dry.ok'): sys.exit('run a clean dry run first: merge_dupes.py dry')
        if os.path.exists(f'{D}/apply.out'): sys.exit('already applied')
    open(f'{D}/merge.sh','w',encoding='utf-8').write(script())
    r=subprocess.run(SSH+[f'bash -s -- {mode}'],stdin=open(f'{D}/merge.sh','rb'),capture_output=True,text=True)
    out=r.stdout; open(f'{D}/{mode}.out','w',encoding='utf-8').write(out+'\n--- stderr\n'+r.stderr)
    old={l.split('\t')[1]:l.split('\t') for l in out.splitlines() if l.startswith('OLD\t')}
    new={l.split('\t')[1]:l.split('\t') for l in out.splitlines() if l.startswith('NEW\t')}
    ok='ERROR' not in out and f'MODE {mode} DONE' in out
    for l in out.splitlines():
        if l.startswith(('BACKUP','ERROR')): print(l[:200])
    for t,(k,dd) in MERGE.items():  # every copy must exist with its title before, and one post after
        seen=all(old.get(str(i),[None]*3)[2]==t for i in [k]+dd); n=new.get(t)
        good=seen and bool(n) and n[2]=='1' and n[3]==str(k) and n[4]=='NULL'; ok&=good
        print(f"  {'ok ' if good else 'BAD'} {t} | keep {k}, delete {dd} | after: {n[2:] if n else '-'}")
    for i,(o,nn) in RENAME.items():
        n=new.get(nn); good=old.get(str(i),[None]*3)[2]==o and bool(n) and n[3]==str(i); ok&=good
        print(f"  {'ok ' if good else 'BAD'} post {i}: '{o}' -> '{nn}' | after: {n[2:] if n else '-'}")
    if not ok:
        os.replace(f'{D}/{mode}.out',f'{D}/{mode}.failed.out'); sys.exit(f'{mode.upper()} FAILED; see {D}/{mode}.failed.out')
    if mode=='dry':
        open(f'{D}/dry.ok','w').write('ok'); print(f'DRY RUN OK: {len(ids())} copies would be deleted, 1 post renamed. Rolled back.'); return
    ins=out.split('RESTORE-BEGIN',1)[1].split('RESTORE-END',1)[0].strip().splitlines()
    with open(f'{D}/restore.sql','w',encoding='utf-8') as f:
        f.write('-- Puts the deleted copies back, and undoes the titlerepeat and title changes. Run only if the owner asks.\nSET NAMES utf8mb4;\n')
        f.write('\n'.join(ins)+'\n')
        for i,(o,_) in RENAME.items(): f.write(f"UPDATE posts SET title = {hx(o)}, titlerepeat = {old[str(i)][3]} WHERE id = {i};\n")
        for t,(k,_) in MERGE.items(): f.write(f"UPDATE posts SET titlerepeat = {old[str(k)][3]} WHERE id = {k};\n")
    print(f'APPLIED: {len(ins)} deleted rows saved in {D}/restore.sql (expected {len(ids())}). next: merge_dupes.py verify')

def slug(t): return re.sub(r'[\W_]+','-',re.sub("['’‘ʼ`´]",'',t.lower())).strip('-')
def get(u):
    r=subprocess.run(['curl','-s','-o','/dev/null','--max-time','30','-w','%{http_code} %{redirect_url}',u],capture_output=True,text=True)
    return r.stdout
def verify():
    base='https://crimewiki.site/post/'
    for t,(k,dd) in MERGE.items():
        print(f"{t}: clean URL -> {get(base+urllib.parse.quote(slug(t)))} | /1 -> {get(base+urllib.parse.quote(slug(t))+'/1')} | /post/{k} -> {get(base+str(k))}")
    for i,(o,n) in RENAME.items():
        print(f"{n}: {get(base+urllib.parse.quote(slug(n)))} | /post/{i} -> {get(base+str(i))}")

a=sys.argv[1] if len(sys.argv)>1 else ''
run(a) if a in('dry','apply') else verify() if a=='verify' else print(__doc__)
