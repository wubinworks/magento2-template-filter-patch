# Magento 2 Template Filter Patch for CVE-2022-24086, CVE-2022-24087

**Magento 2 patch for CVE-2022-24086, CVE-2022-24087. Fix the RCE vulnerability and related bugs by performing deep template variable escaping. If you cannot upgrade Magento or cannot apply the official patches, try this one.**

<a href="https://www.wubinworks.com/template-filter-patch.html" target="_blank"><img src="https://raw.githubusercontent.com/wubinworks/home/master/images/Wubinworks/TemplateFilterPatch/template-filter-patch.jpg" alt="Wubinworks CVE-2022-24086 CVE-2022-24087 Patch" title="Wubinworks CVE-2022-24086 CVE-2022-24087 Patch"/></a>

## Background

[CVE-2022-24086, CVE-2022-24087](https://nvd.nist.gov/vuln/detail/cve-2022-24086) was discovered in the beginning of 2022. For Magento 2.4 releases, all versions <= 2.4.3-p1 are affected by this Remote Code Execution(RCE) vulnerability. 2 [official isolated patches](https://helpx.adobe.com/security/products/magento/apsb22-12.html) were released on February 2022.

However, even in late 2024, we are still receiving consultations regarding this issue and their hacked stores were identified that this vulnerability was exploited. Most observed attacks were performed by inputting a string that contains `template directive`.

The most typical ways are making use of the checkout process, triggering an email sending with the email containing user controlled fields, etc.

We release this patch due to this widespread attack and some stores still having difficulties to upgrade or apply the 2 official patches.

While making this patch as an extension, we keep compatibility in mind. So it should work on all Magento 2.4 versions.

## Features

 - Fixed the RCE caused by malicious user data
 - Fixed an [Unintended User Data Parsing Bug](https://github.com/magento/magento2/issues/39353)
 - Maintains compatibility as much as possible for old templates(see [Template Compatibility Section](#template-compatibility) below)

## Template Compatibility

Although the [official documentation](https://web.archive.org/web/20220710211400/https://developer.adobe.com/commerce/frontend-core/guide/templates/email-migration/) says "methods can no longer be called from variables from either the var directive or when used as parameters", but  as we confirmed, even in the latest version(2.4.7-p3), calling "Getter" method on Data Object and calling `getUrl` method on Email Template Object(`\Magento\Email\Model\AbstractTemplate`) are still allowed.

This patch(extension) also keeps the above features. So `{{var data_object.something}}` and `{{var data_object.getSomething()}}` are both OK and equivalent.

`getUrl` example:

```
{{var this.getUrl($store,'route_id/controller/action',[_query:[param1:$obj.param1,param2:$obj.param2],_nosid:1])}}
```

**In summary, after installing this extension:**

 - Objects which are not `\Magento\Framework\DataObject` or its child instance cannot be accessed
 - Only "Getter" methods are allowed on `\Magento\Framework\DataObject` and its child instances
 - `getUrl` method is only working on `this`

## Technical Info

### Official Approach

##### >=2.4.3-p2

Removed `LegacyResolver` to stop the RCE.

##### >=2.4.4-p2 || >=2.4.5-p1

Introduced "deferred directive with signature" for child template. We are unsure if it has any security enhancement.

##### Latest(2.4.7-p3)

Still has an unfixed bug([#39353](https://github.com/magento/magento2/issues/39353)).

### Our Approach

Use "deep template variable escaping" before the template filtering process. `LegacyResolver` will only receive escaped user data and hence can be kept.

# Requirements

**Magento 2.4**

# Installation

**`composer require wubinworks/module-template-filter-patch`**

## ♥

If you like this extension or this extension helped you, please ★star☆ this repository.

You may also like:  
[Magento 2 patch for CVE-2024-34102(aka CosmicSting)](https://github.com/wubinworks/magento2-cosmic-sting-patch)
