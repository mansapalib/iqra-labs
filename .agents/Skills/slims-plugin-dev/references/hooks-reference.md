# SLiMS Plugin Hooks Reference

This document details all available hook constants and event tags in SLiMS (Senayan Library Management System) 9 Bulian / modern core.

---

## 1. System & Admin Session Lifecycle

| Hook Constant | Hook Tag | Description | Typical Parameters |
| :--- | :--- | :--- | :--- |
| `Plugins::SYSCONFIG_ALL_INIT` | `'sysconfig_all_init'` | Fired during global system configuration initialization. | `(&$sysconf)` |
| `Plugins::ADMIN_SESSION_AFTER_START` | `'admin_session_after_start'` | Fired immediately after an admin session is started/authenticated. | `($_SESSION)` |
| `Plugins::ADMIN_AFTER_MODULE_LOADED` | `'admin_after_module_loaded'` | Fired after the active module script is loaded. | `($module_name)` |
| `Plugins::ADMIN_AFTER_CONTENT_LOADED` | `'admin_after_content_loaded'` | Fired after the content template is rendered. | `()` |
| `Plugins::CONTENT_BEFORE_LOAD` | `'before_content_load'` | Fired before a main content block is loaded. | `(&$content)` |
| `Plugins::CONTENT_AFTER_LOAD` | `'after_content_load'` | Fired after a main content block is loaded. | `(&$content)` |
| `Plugins::MODULE_MAIN_MENU_INIT` | `'module_main_menu_init'` | Allows injecting new top-level admin modules into navigation bar. | `(&$module_list)` |

---

## 2. Bibliographic Module (`bibliography`)

| Hook Constant | Hook Tag | Description | Typical Parameters |
| :--- | :--- | :--- | :--- |
| `Plugins::BIBLIOGRAPHY_INIT` | `'bibliography_init'` | Initialized when entering the bibliography module. | `()` |
| `Plugins::BIBLIOGRAPHY_BEFORE_SAVE` | `'bibliography_before_save'` | Fired before a new book record is inserted into `biblio`. | `(&$data)` |
| `Plugins::BIBLIOGRAPHY_AFTER_SAVE` | `'bibliography_after_save'` | Fired after a new book record is saved. | `($biblio_id, $data)` |
| `Plugins::BIBLIOGRAPHY_BEFORE_UPDATE`| `'bibliography_before_update'`| Fired before an existing book record is updated. | `($biblio_id, &$data)` |
| `Plugins::BIBLIOGRAPHY_AFTER_UPDATE` | `'bibliography_after_update'` | Fired after an existing book record is updated. | `($biblio_id, $data)` |
| `Plugins::BIBLIOGRAPHY_BEFORE_DELETE`| `'bibliography_before_delete'`| Fired before a book record is removed. | `($biblio_id)` |
| `Plugins::BIBLIOGRAPHY_AFTER_DELETE` | `'bibliography_after_delete'` | Fired after a book record is removed. | `($biblio_id)` |
| - | `'bibliography_on_delete'` | Fired on deletion event handler. | `($biblio_id)` |
| `Plugins::BIBLIOGRAPHY_CUSTOM_FIELD_FORM` | `'advance_custom_field_form'` | Injects custom input elements into the Biblio edit form. | `(&$form_object)` |
| `Plugins::BIBLIOGRAPHY_CUSTOM_FIELD_DATA` | `'advance_custom_field_data'` | Handles processing/saving of custom biblio fields. | `($biblio_id, $post_data)` |
| `Plugins::BIBLIOGRAPHY_BEFORE_DATAGRID_OUTPUT` | `'bibliography_before_datagrid_output'` | Modifies bibliographic datagrid before rendering. | `(&$datagrid)` |

---

## 3. Membership Module (`membership`)

| Hook Constant | Hook Tag | Description | Typical Parameters |
| :--- | :--- | :--- | :--- |
| `Plugins::MEMBERSHIP_INIT` | `'membership_init'` | Initialized when entering membership module. | `()` |
| `Plugins::MEMBERSHIP_BEFORE_SAVE` | `'membership_before_save'` | Fired before a new member is created. | `(&$data)` |
| `Plugins::MEMBERSHIP_AFTER_SAVE` | `'membership_after_save'` | Fired after a new member is successfully saved. | `($member_id, $data)` |
| `Plugins::MEMBERSHIP_BEFORE_UPDATE` | `'membership_before_update'` | Fired before member record is updated. | `($member_id, &$data)` |
| `Plugins::MEMBERSHIP_AFTER_UPDATE` | `'membership_after_update'` | Fired after member record is updated. | `($member_id, $data)` |
| `Plugins::MEMBERSHIP_CUSTOM_FIELD_FORM` | `'membership_custom_field_form'` | Injects custom input elements into member edit form. | `(&$form_object)` |
| `Plugins::MEMBERSHIP_CUSTOM_FIELD_DATA` | `'membership_custom_field_data'` | Handles processing/saving of custom member fields. | `($member_id, $post_data)` |

---

## 4. Circulation Module (`circulation`)

| Hook Constant | Hook Tag | Description | Typical Parameters |
| :--- | :--- | :--- | :--- |
| `Plugins::CIRCULATION_AFTER_START_TRANSACTION` | `'circulation_after_start_transaction'` | Fired when circulation counter is opened for a member. | `($member_id)` |
| `Plugins::CIRCULATION_AFTER_SUCCESSFUL_TRANSACTION` | `'circulation_after_successful_transaction'` | Fired after a loan, return, or extend transaction succeeds. | `($member_id, $item_code, $type)` |
| `Plugins::OVERDUE_NOTICE_INIT` | `'overduenotice_init'` | Overdue notification processor. | `(&$notices)` |
| `Plugins::DUEDATE_NOTICE_INIT` | `'duedate_init'` | Upcoming due-date notification processor. | `(&$notices)` |

---

## 5. System Configuration (`system`)

| Hook Constant | Hook Tag | Description | Typical Parameters |
| :--- | :--- | :--- | :--- |
| `Plugins::SYSTEM_BEFORE_CONFIGFORM_PRINTOUT` | `'system_before_configform_printout'` | Injects custom configuration inputs into system settings. | `(&$form)` |
| `Plugins::SYSTEM_BEFORE_CONFIG_SAVE` | `'system_before_config_save'` | Intercepts config before writing to database/file. | `(&$data)` |
| `Plugins::SYSTEM_AFTER_CONFIG_SAVE` | `'system_after_config_save'` | Fired after system config is committed. | `($data)` |

---

## 6. How to Use Hooks in Code

### Static Invocation (Recommended)
```php
use SLiMS\Plugins;

Plugins::hook(Plugins::CIRCULATION_AFTER_SUCCESSFUL_TRANSACTION, function($member_id, $item_code, $type) {
    // Audit logging or webhook notification
    \SLiMS\DB::getInstance()->prepare("INSERT INTO custom_trans_log (member_id, item_code, trans_type) VALUES (?, ?, ?)")
        ->execute([$member_id, $item_code, $type]);
});
```

### Specifying Execution Priority / Sequence
```php
// Sequence defaults to 9999. Use lower numbers (e.g. 10, 50) to run earlier:
Plugins::hook(Plugins::BIBLIOGRAPHY_BEFORE_SAVE, function(&$data) {
    $data['title'] = trim($data['title']);
}, 10);
```
