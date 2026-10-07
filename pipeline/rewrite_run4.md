Run notes for this batch (owner approved, 7 October 2026; 20 topics: 140-159). Follow pipeline/rewrite_prompt.md, with these changes:
- Subject first: before writing, confirm from the pages you opened that they are about the subject the title names (the same person, group or event, with matching dates and place). A namesake is a different subject. In the last run one writer wrote about the wrong man with the same surname. If you cannot confirm the subject, block with the reason.
- Searching: up to 6 searches. Use the WebSearch tool first. If WebSearch refuses or reports a limit, switch for the rest of the topic to `python3 pipeline/search.py "<query>" 12` (Brave, then DuckDuckGo, then Bing; prints URLs; it waits and retries once if no engine answers). Write search.py queries like a search box: names, places, years, "court", "inquiry", "sentenced".
- Fetching: up to 3 fetchmany calls of at most 10 URLs each, plus the Wikipedia lead fetch.
- For a page that returns 403 or THIN, try its Internet Archive copy: https://web.archive.org/web/2024/<original url>.
- Wikipedia mirrors (wikimili, grokipedia, bharatpedia, wikiwand, dbpedia, alchetron, everybodywiki and the like) are Wikipedia: never fetch or cite them as sources.
- Prefer court and government records, university and museum pages, books on archive.org, and long-form journalism.
- Large PDFs: search them by keyword; do not read them whole.
- Keep the exact title. The 1,000-word floor of real, sourced content still holds; never pad. If you cannot reach it, block with the reason.
- About 19 other writers are working on other topics at the same time; touch only your own topic's files.
