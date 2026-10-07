Run notes for this batch (owner approved, 4 October 2026; 40 topics: 88, 90, 96, 103-139). Follow pipeline/rewrite_prompt.md, with these changes:
- Searching: up to 5 searches. Use the WebSearch tool first. If WebSearch refuses or reports a limit, switch for the rest of the topic to `python3 pipeline/search.py "<query>" 12` (Brave, then DuckDuckGo, then Bing; prints URLs). Write search.py queries like a search box: names, places, years, "court", "inquiry", "sentenced".
- Fetching: up to 3 fetchmany calls of at most 10 URLs each, plus the Wikipedia lead fetch.
- For a page that returns 403 or THIN, try its Internet Archive copy: https://web.archive.org/web/2024/<original url>.
- Wikipedia mirrors (wikimili, grokipedia, bharatpedia, wikiwand, dbpedia, alchetron, everybodywiki and the like) are Wikipedia: never fetch or cite them as sources.
- Prefer court and government records, university and museum pages, books on archive.org, and long-form journalism.
- Large PDFs: search them by keyword; do not read them whole.
- Keep the exact title. The 1,000-word floor of real, sourced content still holds; never pad. If you cannot reach it, block with the reason.
- Retries 88, 90, 96: an earlier writer blocked these for lack of sources (its reasons are in blocked/retried-20261004/). Pages it already saved are in your research folder; read them, then search for the missing material its reason names.
- About 39 other writers are working on other topics at the same time; touch only your own topic's files.
