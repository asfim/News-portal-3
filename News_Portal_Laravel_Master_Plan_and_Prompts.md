

# নিউজ পোর্টাল: Laravel রূপান্তরের পূর্ণ পরিকল্পনা ও প্রম্পট

প্রস্তুত: ৮ নভেম্বর ২০২৬ | H Tech Provision IT
ভিত্তি: ব্যবহারকারীর দেওয়া `news portal3.zip`। এটি implementation specification; তৈরি হয়ে যাওয়া Laravel application বা executable migration নয়।

## 1. ফাইল পড়ে যা পাওয়া গেছে

ZIP-এ dependency folder বাদে ১০টি ফাইল: `prothomalo-clone.html`, ৭টি PNG preview/reference, `package.json`, `package-lock.json`। মূল HTML 4,968,788 bytes, প্রায় 4.74 MiB। package.json-এ শুধু puppeteer-core dependency আছে; এটি backend নয়। কোনো PHP application, composer.json, Laravel routes/controllers, database migration/SQL dump বা admin source নেই।

HTML-এ ২০টি script element, ৮টি inline style block, ১১৪টি img element পাওয়া গেছে; ৭টি img-এর src data URI। `static-page` JSON একাই 3,294,203 characters; top-level keys qt এবং header। এটি সংরক্ষিত page state, SQL database নয়। ছবির সংখ্যা distinct news বা distinct image-এর সংখ্যা নয়। মূল সাইটে নির্দেশিত ৪৬০টি anchor পাওয়া গেছে; অন্য সহযোগী সাইটের লিংকও আছে। অনেক ছবি, font এবং CSS বাইরের host-নির্ভর।

পর্যালোচনা পদ্ধতি: archive inventory, HTML/embedded script ও JSON structure, দৃশ্যমান text/link/style এবং সংযুক্ত local_clone_preview.png দেখা হয়েছে। লাইভ browser interaction, login/search বা backend কার্যকারিতা পরীক্ষা করা হয়নি। Preview পুরোনো অবস্থার হতে পারে; HTML-এ পরে header override CSS আছে।

| যা আছে | ফাইলে পাওয়া অবস্থা | Laravel-এ করণীয় |
|---|---|---|
| লোগো, প্রধান navigation, header-এর featured stories | markup ও CSS | site settings, menu manager, curated section |
| Login, Search, Eng, E-paper | interface/link উপস্থিত | নিজস্ব route ও functionality তৈরি |
| কেন্দ্রীয় lead story, পাশের সংবাদ, thumbnails | homepage presentation | database query ও manual lead selection |
| সর্বশেষ, বাংলাদেশ, রাজনীতি, বিশ্ব, বাণিজ্য, মতামত, খেলা, বিনোদন, চাকরি, জীবনযাপন | navigation/content blocks | category/subcategory CMS |
| শিক্ষা, প্রযুক্তি, গ্যাজেটস, ধর্ম, অন্য আলো, নাগরিক সংবাদ, দূর পরবাস | homepage blocks/links | configurable categories/collections |
| আলোচিত বিষয় | topic links | tags ও featured topic order |
| পঠিত, আলোচিত, সুখবর | tab labels/content | views, approved comments, curated collection |
| আমার এলাকার খবর | বিভাগ/জেলা/উপজেলা controls | relational locations + filtered news |
| আপনার জন্য | visible section | প্রথমে editor picks; পরে preference-based selection |
| ছবি ও ভিডিও | thumbnails/gallery presentation | gallery items, video metadata ও detail pages |
| সকালের খবর, সন্ধ্যার খবর | collection links | scheduled/manual collections |
| সরাসরি | সংবাদে live label | live updates module; sports score API আলাদা |
| বিজ্ঞাপনের জায়গা, footer, newsletter ও social/app links | markup/placeholder/external links | ad manager, pages, settings, subscriber module |
| পুরোনো analytics/identity | GTM, Facebook pixel, Chartbeat, Google sign-in, organization JSON-LD | পুরোনো configuration সরিয়ে নিজের configuration |

গুরুত্বপূর্ণ: বিস্তারিত সংবাদ, category listing, author archive এবং admin panel-এর আলাদা template ZIP-এ নেই। এগুলো একই visual language-এ নতুন করে বানাতে হবে। হোমপেজে কোনো button দেখা মানেই সেটির backend আছে—এমন নয়।

## 2. প্রকল্পের লক্ষ্য ও সীমা

একটি Bangla-first, single-publication news portal: admin থেকে সংবাদ, লেখক, category, photo/video, homepage layout, menu, বিজ্ঞাপন ও SEO নিয়ন্ত্রণ করা যাবে। Public frontend হবে server-rendered Blade, যাতে সংবাদ সরাসরি HTML-এ আসে।

বর্তমান layout-এর সাদা background, black typography, restrained red accents, lead grid, thin dividers ও category strips বজায় রাখুন। নিজের প্রকাশনার নাম/logo/footer ব্যবহার করুন। মূল প্রকাশনার copied article/photo/font production-এ ব্যবহারের আগে প্রয়োজনীয় অনুমতি নিশ্চিত করুন; demo-তে নিজস্ব sample content ব্যবহার করুন।

প্রথম release: Bangla content। English আলাদা translation workflow হিসেবে পরের ধাপ; Eng button আগে থেকেই functional বলে দেখাবেন না। Native app, paywall, subscription billing, automatic scraping, AI writing ও live sports scores প্রথম release-এর অংশ নয়।

## 3. Stack ও architecture

- Laravel 13, compatible patched PHP 8.3–8.5; hosting ও CLI PHP একই compatible version নিশ্চিত করুন। Official Laravel 13 minimum PHP 8.3।
- MySQL 8+, InnoDB, utf8mb4; application timestamps UTC, UI ও scheduler input Asia/Dhaka।
- Blade + Vite + modular CSS + অল্প JavaScript; admin-এ Bootstrap 5.3 ব্যবহার করা যেতে পারে। Public design-এ existing style পুনর্গঠন করুন, framework default দিয়ে layout বদলাবেন না।
- Authentication, FormRequest validation, Laravel Policies; RBAC package নিলে Laravel 13 compatibility যাচাই করে version lock করুন।
- Database queue/cache দিয়ে শুরু; load ও hosting অনুযায়ী Redis পরে। Optional Redis ছাড়া core app চালাতে হবে।
- Local/public disk বা S3-compatible storage; DB-তে file path/metadata, binary/base64 নয়। Rich-text editor-এর server-side HTML sanitization আবশ্যক।
- Controller → service/action → Eloquent query → Blade component। Public query-র কেন্দ্রীয় Published scope থাকবে।

প্রস্তাবিত source layout:

```text
app/Http/Controllers/Frontend/
app/Http/Controllers/Admin/
app/Http/Requests/Admin/
app/Models/
app/Policies/
app/Actions/News/
app/Services/Homepage/
app/Services/Media/
app/Jobs/
app/Console/Commands/
resources/views/layouts/
resources/views/components/news/
resources/views/components/sections/
resources/views/frontend/
resources/views/admin/
resources/css/
resources/js/
routes/web.php
routes/admin.php
database/migrations/
database/factories/
database/seeders/
tests/Feature/
```

## 4. Public frontend pages

| Route | কাজ |
|---|---|
| `/` | editable homepage |
| `/latest` | সর্বশেষ প্রকাশিত সংবাদ, pagination |
| `/category/{slug}` | category/subcategory listing |
| `/news/{public_id}/{slug}` | canonical detail URL; immutable public_id |
| `/topic/{slug}` | tag/topic news |
| `/author/{slug}` | profile ও সংশ্লিষ্ট সংবাদ |
| `/search?q=&category=&from=&to=` | Bangla query, filters, pagination |
| `/archive?date=` | নির্দিষ্ট দিনের সংবাদ |
| `/location/{slug}` | বিভাগ/জেলা/উপজেলার news |
| `/photos`, `/videos` | media-type listing; detail একই news route |
| `/collection/{slug}` | সুখবর, সকাল/সন্ধ্যা, বিশেষ আয়োজন |
| `/page/{slug}` | about, privacy, terms, editorial policy |
| `/contact` | contact form + office/editor details |
| `/login`, `/register`, `/account` | reader account, bookmark; module enabled হলে |
| `/feed.xml`, `/sitemap.xml`, `/robots.txt` | feeds ও discoverability |
| `/epaper` | Phase 2 enabled হলে |

News detail: breadcrumb, kicker, headline, subtitle, author/byline, location, publish/modified time, cover caption/credit, body, inline images, approved embeds, share/copy-link/print, font controls, topics, related news, correction note এবং moderated comments।

সব listing-এ empty state; missing/unpublished article-এ 404; old slug হলে বর্তমান canonical URL-এ 301। Preview route authenticated/authorized, noindex ও private/no-store হবে। Public route-এ preview query parameter দিয়ে draft প্রকাশ করা যাবে না।

## 5. Admin panel menu ও দায়িত্ব

1. Dashboard: draft/review/published/scheduled count, top content, queue failure alert।
2. News: সব সংবাদ, নতুন সংবাদ, আমার draft, review queue, scheduled, corrections, trash।
3. Taxonomy: categories/subcategories, tags/topics, locations, author profiles।
4. Media: upload library, gallery order/captions, video provider/video ID।
5. Homepage: section order, on/off, title, layout preset, source, pinned stories, item limit।
6. Collections: morning/evening, good news, special coverage।
7. Navigation: main menu, mobile menu, footer menu।
8. Advertisements: slots, campaigns/creatives, schedule, target category।
9. Engagement: comment moderation, readers, bookmarks overview; newsletter Phase 2।
10. Pages: about/contact/privacy/terms/editorial team।
11. Users & permissions: staff invitation/account, roles, author linkage।
12. Settings: identity, logo/favicon, contact/social, timezone/display, SEO defaults।
13. Operations: audit trail, failed jobs, backup status; raw secrets প্রকাশ নয়।

| Role | অনুমতি |
|---|---|
| Super Admin | সব settings, staff, permissions, publishing |
| Admin | দৈনন্দিন CMS management; sensitive permissions আলাদাভাবে |
| Editor | review, edit, approve, schedule, publish, correction/unpublish |
| Reporter | নিজের draft/create/edit/submit; নিজে publish নয় |
| Moderator | comment review/hide; article publish নয় |
| Ad Manager | ads/slots; article বা user permissions নয় |
| Reader | নিজের account/bookmark/comment; admin নয় |

Role label নয়, permission + ownership policy দিয়ে প্রতিটি endpoint সুরক্ষিত করুন। Menu লুকানো access control নয়।

## 6. Editorial workflow

Draft → In Review → Approved → Published অথবা Scheduled → Published। Editor review থেকে changes requested করলে Draft-এ ফেরত যাবে, reason log হবে। Published → Archived/Unpublished editor action; audit ও cache invalidation হবে।

- Reporter নিজের draft update করবেন; review চললে change request বা editor action লাগবে।
- `news` row হলো current canonical/public snapshot। `news_revisions`-এ সম্পূর্ণ proposed snapshot থাকবে। Published story-এর reporter edit pending revision-এ যাবে; approval ছাড়া live row বদলাবে না।
- Editor publish action transaction-এ validation, row lock/version check, revision approval, live snapshot update, status history এবং outbox event লিখবে।
- First publish time immutable; substantive correction-এ `content_updated_at` ও public correction note বদলাবে।
- Schedule input Dhaka time থেকে UTC convert; প্রতি মিনিটে due rows process; status ও version পুনরায় check, repeated run-safe।
- Publish eligibility: headline, unique URL, primary category, author, meaningful body অথবা valid gallery/video, media credit/alt।
- Published scope: status=published, published_at<=now, deleted_at IS NULL। Home, search, related, RSS, sitemap, media listing—সবখানে একই rule।
- Retract করলে cache purge, search removal এবং old pinned/related placements থেকে hide।

## 7. Database convention

সব মূল table-এ `id BIGINT UNSIGNED`, `created_at`, `updated_at`; FK একই type। নিচের field-list-এ nullable `?`, unique `U`, JSON `J`। enum-like status string + application enum/validation; relation-এর জন্য JSON নয়। Content public key হিসেবে news.public_id ULID unique। Slug NFC-normalized, deterministic collision suffix; unsupported/empty slug হলে public_id fallback।

### 7.1 Identity ও taxonomy

| Table | প্রধান fields | Constraints/relations |
|---|---|---|
| users | name, email U, password, email_verified_at?, status, last_login_at? | deactivate staff; historical author attribution রেখে দিন |
| roles, permissions | name, guard_name | unique(name,guard_name); package schema হলে তার migration অনুসরণ |
| model_has_roles, model_has_permissions, role_has_permissions | package-defined keys | duplicate mapping নিষিদ্ধ |
| author_profiles | user_id?, name, slug U, bio?, designation?, avatar_media_id?, social_links J?, is_active | user_id nullable unique; guest writer-এর login লাগবে না |
| categories | parent_id?, name, slug U, description?, sort_order, is_active, seo_title?, seo_description? | self FK; cycle prevention, assigned category hard-delete restrict |
| tags | name, slug U, description?, is_featured, sort_order | category থেকে পৃথক topic |
| locations | parent_id?, type, name, slug U, code? | type=division/district/upazila; parent hierarchy validate |

News একটি সর্বাধিক নির্দিষ্ট location-এ যুক্ত হবে; division filter-এ descendants অন্তর্ভুক্ত হবে। প্রথমে parent-child traversal/cache যথেষ্ট; district/upazila নাম hardcode নয়।

### 7.2 News ও content

| Table | প্রধান fields | Constraints/relations |
|---|---|---|
| news | public_id U, slug U, title, kicker?, subtitle?, excerpt?, body_html LONGTEXT, body_plain LONGTEXT, type, status, primary_category_id, location_id?, cover_media_id?, cover_alt?, cover_caption?, cover_credit?, source_name?, source_url?, created_by, updated_by?, approved_by?, published_by?, scheduled_at?, published_at?, content_updated_at?, comments_enabled, seo_title?, seo_description?, og_media_id?, lock_version, deleted_at? | type=article/opinion/photo/video/live; primary category FK; public_id immutable |
| news_category | news_id, category_id | unique pair; primary category অবশ্যই এই membership-এ থাকবে |
| news_tag | news_id, tag_id | unique pair; reverse index(tag_id,news_id) |
| news_author | news_id, author_profile_id, sort_order, role? | unique pair; multi-author order |
| news_related | news_id, related_news_id, sort_order | unique pair; self-link নিষিদ্ধ |
| news_revisions | news_id, version, snapshot J, status, edited_by, approved_by?, approved_at?, change_note? | unique(news_id,version); snapshot-এ content, categories, authors, media references |
| news_status_histories | news_id, from_status?, to_status, actor_id?, reason?, revision_id? | append-only |
| news_corrections | news_id, revision_id, public_note, corrected_by, corrected_at | correction published হলে public note |
| publication_events | news_id, revision_id, event_type, payload J, processed_at?, attempts | unique(news_id,revision_id,event_type); durable outbox |

`news_related` curated related links; যথেষ্ট না হলে same tags/category থেকে auto-fill, current news exclude। Article type অনুযায়ী আলাদা post table নয়; news table editorial lifecycle-এর একক source।

### 7.3 Media

| Table | প্রধান fields | Constraints/relations |
|---|---|---|
| media | uploaded_by, disk, path U, original_name, mime_type, size_bytes, width?, height?, checksum?, alt_text?, caption?, credit?, license_note?, status | file upload validate; SVG প্রথমে disallow |
| media_variants | media_id, variant, path, width, height, format, size_bytes | unique(media_id,variant); thumb/card/cover |
| news_media | news_id, media_id, usage, sort_order, alt_override?, caption_override?, credit_override? | gallery/inline attachments; explicit FK |
| news_videos | news_id U, provider, provider_video_id?, media_id?, thumbnail_media_id?, duration_seconds?, transcript? | allowlisted provider + validated ID; arbitrary iframe HTML নয় |
| live_updates | news_id, title?, body_html, author_id, occurred_at, status, pinned_at? | Phase 2; parent type=live |

Media processing queued; failed conversion fallback original supported image। Referenced media hard-delete block; detach/reassign আগে। Replacement image-এ old URL overwrite নয়, new versioned path।

### 7.4 Homepage, collections, menus

| Table | প্রধান fields | Constraints/relations |
|---|---|---|
| homepage_sections | key U, title, layout, source_type, category_id?, collection_id?, item_limit, sort_order, is_active, settings J | layout preset allowlist; source=latest/category/collection/popular/manual |
| homepage_section_items | section_id, news_id, position, starts_at?, ends_at? | unique(section_id,news_id); ordered pin placement |
| collections | name, slug U, description?, starts_at?, ends_at?, is_active | morning/evening/special/good-news |
| collection_news | collection_id, news_id, sort_order | unique pair |
| menus | name, key U, is_active | primary/mobile/footer |
| menu_items | menu_id, parent_id?, label, target_type, category_id?, page_id?, collection_id?, external_url?, sort_order, open_new_tab, is_active | typed destination; relevant FK validate; tree cycles block |
| breaking_items | news_id?, label?, url?, starts_at, ends_at?, priority, is_active | news থাকলে published eligibility; এটিকে নতুন proposed module ধরুন |
| settings | group, key, value J, is_public | unique(group,key); secret credentials এই public settings-এ নয় |
| pages | title, slug U, body_html, status, seo_title?, seo_description?, published_at? | public pages sanitization |

Homepage rule: valid pinned news প্রথমে, তারপর source query দিয়ে limit পূরণ; একই section-এ duplicate নয়। Lead/top region-এ cross-section duplicate exclusion configurable। Source disabled/empty হলে section hide বা editor-approved fallback। Admin arbitrary SQL বা Blade লিখতে পারবেন না।

### 7.5 Ads, readership ও operations

| Table | প্রধান fields | Constraints/relations |
|---|---|---|
| ad_slots | key U, name, desktop_width?, desktop_height?, mobile_width?, mobile_height?, is_active | header/sidebar/in-article/footer |
| advertisements | title, media_id?, destination_url?, provider_type, provider_slot_id?, starts_at, ends_at?, priority, status, sponsor_name? | image ad default; arbitrary JS editor input নয় |
| advertisement_slot | advertisement_id, ad_slot_id | unique pair |
| advertisement_category | advertisement_id, category_id | unique pair; no rows = all categories |
| comments | news_id, user_id, parent_id?, body TEXT, status, moderated_by?, moderated_at?, rejection_reason? | parent একই news-এর; pending default; plain text |
| bookmarks | user_id, news_id | unique pair |
| news_daily_stats | news_id, stat_date, views, unique_views | unique(news_id,stat_date); aggregated counters |
| contact_messages | name, email, subject?, message, status, handled_by? | rate-limit; retention policy |
| audit_logs | actor_id?, action, subject_type, subject_id?, before J?, after J?, request_id? | credentials/full sensitive payload log নয় |
| sessions, password_reset_tokens | framework schema | sessions migration deployment-এর আগে |
| jobs, job_batches, failed_jobs | framework schema | queue observability/retry |
| cache, cache_locks | framework schema | database cache profile হলে |

পঠিত: নির্বাচিত সময়সীমায় news_daily_stats.views; আলোচিত: একই সময়সীমায় approved comment count; সুখবর: curated collection। Views সত্যিকারের unique মানুষ নয়: bot filtering ও short-lived visitor/news dedup cache key ব্যবহার করুন, approximation label দিন। Raw IP স্থায়ীভাবে না রেখে প্রয়োজন হলে rotating salted hash + সীমিত retention। Unique impressions ছাড়াই ad clicks/CTR সঠিক বলে দেখাবেন না; ad measurement আলাদা Phase 2 scope।

### 7.6 Phase 2 extension tables

- newsletter_subscribers: email U, status, confirmation_token_hash?, confirmed_at?, unsubscribe_token_hash, unsubscribed_at?।
- newsletter_campaigns: subject, body_html, status, scheduled_at?, sent_at?।
- newsletter_deliveries: campaign_id, subscriber_id, status, attempts, sent_at?; unique pair।
- epaper_editions: issue_date, edition_key, title, pdf_media_id?, status; unique(issue_date,edition_key)।
- epaper_pages: edition_id, page_number, image_media_id; unique(edition_id,page_number)।
- user_topic_preferences: user_id, tag_id; unique pair; explicit opt-in personalization।
- news_translations: news_id, locale, slug, title, excerpt, body_html, seo fields, status, published_at?; unique(news_id,locale), unique(locale,slug)। English route আলাদা `/en/...`; translation unpublished হলে link hide।
- optional integration_sources/source_items: only licensed feed/API imports; external_id+source_id unique; staged draft review, no silent autopublish।
- optional social_publications: news_id, revision_id, channel, status, remote_id?, attempts; unique(news_id,revision_id,channel) for duplicate prevention।

এগুলো core schema-এর সঙ্গে একসঙ্গে বাধ্যতামূলক নয়। Phase 2 module নিলে corresponding admin/public pages, permissions ও tests যোগ করতে হবে।

## 8. Index, FK ও search strategy

1. news(status,published_at,id), news(status,scheduled_at,id), news(primary_category_id,status,published_at,id), news(location_id,status,published_at,id), news(created_by,status,created_at)।
2. Pivot table-এ unique forward pair + reverse lookup index; daily stats(stat_date,views) report load অনুযায়ী পরীক্ষা।
3. comments(news_id,status,created_at); homepage_sections(is_active,sort_order); live_updates(news_id,status,occurred_at)।
4. Category/author/location deletion referenced থাকলে restrict/deactivate। News soft-delete; permanent purge deliberate operation। News-owned pivots ও revisions hard purge-এ cascade; audit historical reference snapshot হিসেবে টিকে থাকবে।
5. FK cycles এড়াতে migration order: users → media (uploader user) → author/categories/tags/locations/pages/collections → news → pivots/revisions/media variants → menus/sections/ads/engagement। Users.avatar দরকার হলে separate later migration।
6. Bangla search MVP: normalized query, title/excerpt/body_plain-এ parameterized matching, bounded query length, pagination, category/date filters। `%LIKE%` বড় dataset-এ slow—small-data fallback হিসেবে ব্যবহার।
7. MySQL FULLTEXT-এর Bangla tokenization/relevance নিজের corpus দিয়ে benchmark ছাড়া যথেষ্ট বলে ধরে নেবেন না। Scale phase-এ compatible search engine adapter; publish/unpublish indexing queued, backend Published scope দিয়ে result পুনরায় filter।
8. Eager-load category/authors/cover; প্রতি card-এ আলাদা query নয়। Rich body দিয়ে list query ভারী করবেন না।

## 9. Existing HTML conversion steps

1. Original ZIP read-only reference হিসেবে রাখুন; current HTML + existing previews থেকে layout inventory লিখুন।
2. Giant static-page JSON, copied organization schema/canonical, old tracking IDs, Google login fragments ও unrelated anniversary scripts remove করুন। Dependency node_modules Laravel project-এ copy নয়।
3. Reusable header/nav/footer, lead-card, compact-card, category-strip, topic-list, media-card, ad-slot এবং section components বানান। Hash-like original CSS classes-এর জায়গায় maintainable names দিন।
4. Licensed/owned local fonts/assets ব্যবহার করে CSS ও JS Vite-এ নিন। External CSS ছাড়া rendering সম্ভব হতে হবে।
5. প্রথমে synthetic fixtures দিয়ে homepage match করুন; এরপর same components-এ database data bind করুন।
6. Header-এ duplicate login/search markup, override-heavy CSS ও top story visibility check করুন। Small mobile-এ horizontal body overflow নয়।
7. Links `route()` দিয়ে তৈরি; prothomalo domain বা dead `#` internal destination রাখা যাবে না। External footer links শুধু owner-configured হলে।
8. Hero image eager; lower images lazy; width/height reserve; multiple variants ও responsive srcset। Caption/credit হারানো যাবে না।
9. Localized relative time server timestamp থেকে; static “৩ ঘণ্টা আগে” import নয়।
10. Search/mobile menu/tab/gallery actual interaction বানিয়ে keyboard/focus পরীক্ষা করুন।

## 10. SEO, safety, performance

প্রতিটি article-এ own-domain canonical, Open Graph, Twitter card metadata, NewsArticle JSON-LD, author ও image URL। HTML title/description fallback থাকবে। Home-এ নিজের Organization data। Sitemap ও RSS-এ শুধু eligible public records। Admin/preview noindex; robots একা access control নয়। News sitemap যোগ করলে launch-এর সময় Google-এর current requirements যাচাই করে আলাদা implementation করুন; indexing/ranking guarantee নয়।

Rich HTML server-side allowlist sanitize; script/onerror/javascript URL block। Media MIME/content/size validate, executable upload public path-এ নয়। Server-side remote fetch থাকলে SSRF controls। CSRF, rate limits, ownership policies, password hashing, staff account disable/session revoke। Signed preview-ও authorization ছাড়া দীর্ঘস্থায়ী public link নয়।

Cache keys locale + section + filters অনুযায়ী; article publish/edit/unpublish, category/menu/section/ad/settings change হলে যথাযথ invalidate। Authenticated preview shared cache-এ যাবে না। File/database cache-এ unsupported tag API ধরে নেবেন না। TTL safety net রাখুন।

## 11. Hosting ও release

cPanel অথবা VPS দুই profile documented থাকবে। PHP CLI/web compatible, required extensions, writable storage/bootstrap cache, DB user permissions ও document root `/public` নিশ্চিত করুন। `.env` web-accessible হবে না। APP_ENV=production এবং APP_DEBUG=false। Production-এ migrate:fresh/seed-reset endpoint নয়।

Build local/CI-তে; deploy composer.lock/package-lock ও compiled assets। Production dependencies install, backup, reviewed migrations, config/route/view optimization, queue restart, health check। Migration rollback destructive হলে backup restore-based recovery plan। Database এবং media দুইটির backup, periodic restore test।

Scheduler প্রতি মিনিটে `php artisan schedule:run`; supported hosting-এ supervised queue worker। Shared hosting persistent worker না দিলে cron-driven `queue:work --stop-when-empty` bounded timeout/locking এবং overlap prevention; resource/time limit আগে যাচাই। Scheduling বা queue unavailable হলে scheduled publishing ও media processing production-ready নয়।

## 12. Development phases ও acceptance gates

সময়সীমা কাজের পরিমাণভিত্তিক আনুমানিক; একজন অভিজ্ঞ Laravel developer-এর full-time effort, feedback/hosting delay বাদে।

| Phase | Deliverable | আনুমানিক কাজ | Done gate |
|---|---|---|---|
| 0 | archive audit, own brand/assets, page inventory | 1–2 দিন | design reference ও scope documented |
| 1 | app, auth, permissions, migrations/seed | 3–4 দিন | clean test DB migrate; access policies pass |
| 2 | news CMS, revision/review, taxonomy/media | 5–7 দিন | reporter submit → editor publish; safe revision |
| 3 | homepage + dynamic public pages | 5–7 দিন | end-to-end links, filters, mobile layout |
| 4 | homepage builder, ads, comments/bookmarks | 4–6 দিন | admin changes public site-এ সঠিকভাবে দৃশ্যমান |
| 5 | SEO/cache/scheduler/queue/security | 3–5 দিন | draft leakage নেই; jobs retry-safe |
| 6 | QA, staging, deployment guide | 3–4 দিন | acceptance checklist ও restore exercise |

Core production release: প্রায় ২৪–৩৫ কার্যদিবস; reusable existing CMS থাকলে কমতে পারে। Phase 2 extensions আলাদা estimate করুন।

Minimum meaningful tests:

- Reporter অন্যের draft edit বা নিজে publish করতে পারেন না; Reader admin-এ denied।
- Draft/scheduled/archived/deleted news সব public surfaces থেকে hidden।
- Published news-এর pending revision live text পরিবর্তন করে না; approval করলে করে।
- Due scheduled news একাধিক worker/run হলেও একবার transition/outbox event।
- Dhaka midnight date filter ও UTC boundaries ঠিক।
- Slug collision ও rename canonical redirect, no redirect loop।
- Category cycle/invalid district parent reject।
- Malicious HTML/upload/video URL rejected/sanitized।
- Pending comment hidden; different-news parent reply reject; duplicate bookmark blocked।
- Pin/fallback order, unavailable story exclusion ও cache invalidation কাজ করে।
- Search/author/topic/RSS/sitemap-এ withdrawn story ফেরত আসে না।
- 360, 768, 1440px-এ layout, image fallback, long Bangla headlines ও keyboard menu usable।

## 13. Copy-paste Master Prompt

প্রথমে এই document এবং original ZIP coding assistant-কে দিন। নিচের prompt দিন; তারপর একবারে একটি phase prompt ব্যবহার করুন।

```text
Act as a senior Laravel engineer. Build a production-oriented Bangla news
portal for H Tech Provision IT using the attached source archive and this
News_Portal_Laravel_Master_Plan_and_Prompts.md as the specification.

Inspect the archive before editing. It contains one captured homepage,
prothomalo-clone.html, reference PNGs and Puppeteer dependencies, not a
Laravel backend. Do not claim linked article/admin pages already exist.
Preserve the editorial layout language while replacing publisher identity,
external runtime dependencies, tracking IDs and copied sample content.
Do not copy the archive's node_modules into the Laravel application.

Use Laravel 13 with compatible PHP >=8.3, MySQL 8+ utf8mb4, Blade, Vite,
modular CSS/JS, database-backed queues/cache by default, and compatible
RBAC with Policies. Verify framework/package compatibility and pin locks.
Bangla is the release language. Store timestamps UTC, display Asia/Dhaka.
Use the database specification and phased scope in the attached plan.
Do not silently add AI, paid APIs, scraping, subscriptions or native apps.

Build real database-driven pages and an authenticated editorial CMS.
Use FormRequests, explicit permissions and ownership checks, Eloquent
relationships, eager loading, service/action classes and transactions.
Use a centralized published eligibility scope on every public surface.
Pending revisions of published stories must not modify live content.
Scheduled publishing must be idempotent and use a durable publication outbox.
Use original demo news and licensed/local assets, not copied publisher news.

Make all menu items, story links, filters, media actions and admin forms
functional. Hide disabled optional features. No dead placeholder buttons.
Use server-side rich HTML sanitization, upload validation, trusted video
providers, rate limiting and safe secret handling. Store media paths in DB.

Work phase by phase in dependency order. Start with Phase 0 and Phase 1.
Before coding, show the archive findings, environment constraints and exact
planned files. Then implement the authorized phase, run relevant checks,
and report changed files, commands, test results and remaining blockers.
Do not claim a phase complete without working routes and acceptance evidence.
Never run destructive production migration/seed reset commands.
Deliver README, .env.example without secrets, migrations, seeders, tests,
source code, build instructions and cPanel/VPS deployment instructions.
```

## 14. Phase-by-phase implementation prompts

### Prompt 0 — Audit and layout inventory

```text
Inspect every first-party file in the attached ZIP without running bundled
scripts. Identify static markup, embedded JSON, external assets, tracking,
internal/external links and missing pages. Record evidence with filenames
and selectors. Treat embedded state as reference data, not executable logic.
Produce a page/component inventory, responsive design tokens, proposed
Blade component map and source-cleanup checklist. Distinguish observed
features from new proposals. Do not fetch copied stories for seeding.
```

### Prompt 1 — Foundation and relational schema

```text
Implement Phase 1 using the attached schema plan. Verify PHP/Composer/Node
requirements first. Create Laravel app, database/cache/queue migrations,
authentication, role/permission seeders and policies. Add models/migrations
in FK-safe order for the core CMS; omit optional Phase 2 modules for now.
Use immutable news public_id, unique slugs, utf8mb4, indexed publication
queries, explicit pivot uniqueness and documented deletion rules.
Use a separate testing database. Prove migrations run cleanly and ownership
policies reject cross-user draft changes. Do not install incompatible
packages or hardcode staff passwords into the repository.
```

### Prompt 2 — Editorial CMS and publishing

```text
Build category/tag/location/author CRUD, article editor, media library,
news type validation, review queue, schedules, revisions and corrections.
Reporter can submit own drafts; editor approves and publishes. Keep pending
edits separate from the published snapshot. Validate optimistic lock_version
and use transactional publishing with status history and a unique outbox.
Implement a retry-safe due-publication command converting Dhaka inputs to UTC.
Queue image variants after commit. Test permissions, stale updates, duplicate
scheduler runs and draft exclusion. Provide usable admin Blade forms.
```

### Prompt 3 — Public Blade frontend

```text
Rebuild the supplied homepage with clean local Blade components and assets.
Preserve its editorial grid, typography hierarchy, top stories, category
strips, topic list, local-news selector, photo/video blocks and ad spaces.
Bind all content to database queries. Add article, category, author, topic,
search, archive, location, photo/video listing and static pages matching
the same design. Canonical news route uses immutable public_id plus slug.
All public queries must use published eligibility; old slug redirects 301.
Implement mobile nav, keyboard focus, pagination, image fallback and empty
states. Remove duplicate header controls and external publisher URLs.
Validate 360/768/1440px layouts and seed-to-public end-to-end navigation.
```

### Prompt 4 — Homepage, navigation and advertisements

```text
Build admin homepage sections with preset layouts, ordering, enabled state,
source type, category/collection, item limits and timed story pins.
Use pinned-first then eligible auto-fill, deduplication and empty handling.
Implement menus, collections, breaking-items and site identity settings.
Implement ad slots and scheduled image/provider-slot creatives, category
eligibility and responsive sizing. No arbitrary SQL/Blade/editor scripts.
Invalidate relevant caches after changes and test expiry, unpublish and
missing-image scenarios. Keep preview private and uncached publicly.
```

### Prompt 5 — Readers and reporting

```text
Add reader registration/login, bookmarks, moderated plain-text comments,
contact form and aggregated news daily statistics. Enforce same-news parent
comments, unique bookmarks and abuse rate limits. Latest, most-read,
most-discussed and curated good-news tabs must have explicitly different
query rules. Use bounded visitor/news deduplication for approximate views,
exclude obvious bots, avoid retaining raw IP unnecessarily. Do not label
anonymous view counts as exact unique people. Test moderation and privacy.
```

### Prompt 6 — SEO, reliability and release

```text
Add canonical/OG metadata, NewsArticle and Organization JSON-LD, sitemap,
RSS, public robots rules, search indexing hooks, cache invalidation and
queue failure visibility. Verify all feeds exclude unpublished content.
Document cPanel/VPS public root, PHP versions, cron, queue supervision,
nonpersistent-worker fallback, storage, backups and rollback. Run feature
tests and a staging publishing journey. Check own-domain links, no legacy
tracking IDs, no exposed .env, APP_DEBUG=false and no destructive web routes.
Deliver a production-readiness report with actual evidence and blockers.
```

### Prompt 7 — Optional modules, separately selected

```text
Implement only the explicitly selected optional module from the plan:
newsletter, e-paper, live updates, English translations, personalization,
licensed feeds or approved social publishing. First state the required
schema, routes, permissions, external dependencies and costs. Build it
behind an enabled setting, with queues/retries, idempotency and tests.
Unconfigured integrations must remain disabled. Do not pretend a live
score API, email sender or social integration works without verification.
```

## 15. Handover checklist

- Own name/logo, publisher/editor details, contact/social ও licensed assets supplied।
- Admin থেকে নতুন category/news/banner দিলে code edit ছাড়া public site বদলায়।
- News creation → review → publish → correction → withdrawal demonstrated।
- Scheduled publish, queue retry, DB+media restore tested in staging।
- README-তে prerequisites, exact setup commands, environment variables, admin creation, build, worker/cron ও deployment আছে।
- Final source ZIP-এ vendor/node_modules/.env/secrets নেই; lockfiles, build instructions এবং প্রয়োজন হলে compiled public assets আছে।
- Core এবং deferred optional modules আলাদাভাবে acceptance report-এ চিহ্নিত।

## Sources checked

Official Laravel references checked 8 November 2026. Implementation details and schema above are proposed engineering design, not claims copied from documentation.

- Laravel 13 releases / PHP compatibility: https://laravel.com/framework/docs/13.x/releases
- Deployment: https://laravel.com/framework/docs/deployment
- Scheduler: https://laravel.com/framework/docs/scheduling
- Queues: https://laravel.com/framework/docs/queues

Primary project evidence: supplied news portal3.zip → prothomalo-clone.html, package.json, package-lock.json and bundled reference PNGs.
