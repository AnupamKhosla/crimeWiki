#!/usr/bin/env python3
"""Fable use for the usage sidebar: call count and API-priced cost of every
claude-fable call (all projects, main sessions and subagents) in the current
5-hour and weekly plan windows. The plan meter does not split by model, so this
is the closest figure there is. Run in the background by statusline.sh; writes
fable_latest.json. Files already read are cached by size and mtime."""
import calendar, glob, json, os, time

DIR = os.path.dirname(os.path.abspath(__file__))
CACHE, OUT, LOCK = (os.path.join(DIR, n) for n in ('fable_cache.json', 'fable_latest.json', 'fable.lock'))
# USD per MTok: input, 5m write, 1h write, cache read, output (pricing page, 10 Oct 2026)
PRICES = {'claude-fable-5-1': (10, 12.5, 20, 0.25, 50), 'claude-fable-5': (10, 12.5, 20, 1, 50)}

def price(model):
    return next(p for k, p in PRICES.items() if model.startswith(k))

def scan(path):
    """Fable calls in one transcript: {message id: [epoch, usage row]}, max of each field."""
    calls = {}
    for line in open(path, encoding='utf-8', errors='replace'):
        if 'claude-fable' not in line or '"usage"' not in line: continue
        try: j = json.loads(line)
        except Exception: continue
        m = j.get('message') if isinstance(j, dict) else None
        if not isinstance(m, dict) or not isinstance(m.get('usage'), dict) or not j.get('timestamp'): continue
        if not str(m.get('model', '')).startswith('claude-fable'): continue
        u = m['usage']; cc = u.get('cache_creation') or {}
        w5, w1 = cc.get('ephemeral_5m_input_tokens'), cc.get('ephemeral_1h_input_tokens')
        if w5 is None and w1 is None: w5, w1 = u.get('cache_creation_input_tokens') or 0, 0
        row = [u.get('input_tokens') or 0, w5 or 0, w1 or 0, u.get('cache_read_input_tokens') or 0, u.get('output_tokens') or 0]
        t = calendar.timegm(time.strptime(j['timestamp'][:19], '%Y-%m-%dT%H:%M:%S'))
        k = m.get('id') or j['timestamp']
        old = calls.get(k)
        calls[k] = [t, m['model'], [max(a, b) for a, b in zip(old[2], row)] if old else row]
    return calls

def main():
    limits = json.load(open(os.path.join(DIR, 'last_input.json'))).get('rate_limits') or {}
    start5 = limits['five_hour']['resets_at'] - 5 * 3600
    start7 = limits['seven_day']['resets_at'] - 7 * 86400
    try: cache = json.load(open(CACHE))
    except Exception: cache = {}
    fresh, tot = {}, {'five_hour': [0, 0.0], 'seven_day': [0, 0.0]}
    for path in glob.glob(os.path.expanduser('~/.claude/projects/**/*.jsonl'), recursive=True):
        st = os.stat(path)
        if st.st_mtime < start7: continue
        c = cache.get(path)
        if not c or c['size'] != st.st_size or c['mtime'] != st.st_mtime:
            c = {'size': st.st_size, 'mtime': st.st_mtime, 'calls': scan(path)}
        fresh[path] = c
        for t, model, r in c['calls'].values():
            usd = sum(x * p for x, p in zip(r, price(model))) / 1e6
            for key, start in (('five_hour', start5), ('seven_day', start7)):
                if t >= start: tot[key][0] += 1; tot[key][1] += usd
    json.dump(fresh, open(CACHE, 'w'))
    json.dump({'ts': time.strftime('%Y-%m-%dT%H:%M:%S'),
               **{k: {'calls': n, 'usd': round(u, 2)} for k, (n, u) in tot.items()}}, open(OUT, 'w'))

if __name__ == '__main__':
    # One run at a time; a lock older than 5 minutes is from a run that died.
    try:
        if os.path.exists(LOCK) and time.time() - os.path.getmtime(LOCK) > 300: os.rmdir(LOCK)
        os.mkdir(LOCK)
    except OSError: raise SystemExit
    try: main()
    finally: os.rmdir(LOCK)
