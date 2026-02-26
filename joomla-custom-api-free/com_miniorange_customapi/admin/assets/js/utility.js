function add_css_tab(element) {
    jQuery(".mo_customapi_nav_tab_active").removeClass("mo_customapi_nav_tab_active").removeClass("active");
    jQuery(".mo_customapi_nav-tab").removeClass("mo_customapi_nav_tab_active");
    jQuery(".mo_customapi_support-tab").removeClass("mo_customapi_nav_tab_active");
	jQuery(element).addClass("mo_customapi_nav_tab_active");
    jQuery('#create_custom_apis').hide();
    jQuery('#view_current_custom_api').hide();
    jQuery('#create_custom_sql_apis').hide();
    jQuery('#view_current_custom_api').hide();
}

function moCancelForm() {
    jQuery('#cancel_form').submit();
}

function mo_login_page() {
    jQuery('#customer_login_form').submit();
}

function moMediaBack() {
    jQuery('#mo_media_cancel_form').submit();
}

function moCustomUpgrade() {
    jQuery('a[href="#upgrade_plans"]').click();
    add_css_tab("#upgrade_tab");
}

function show_api_creation_window()
{
    jQuery('#API_list').hide();
    jQuery('a[href="#create_custom_apis"]').click();
}

function show_current_api()
{   
    jQuery('a[href="#view_all_apis"]').click();
}

function save_table_name()
{
    document.getElementById("mo_api_name").value = document.getElementById("api_name").value;
    document.getElementById("mo_method_name").value = document.getElementById("api_method").value;
    document.getElementById("mo_table_name").value = document.getElementById("select_table_name").value;
    document.getElementById("SubmitForm1").click();
}

function copyToClipboard() {
    document.getElementById("mo_custom_api_copy_text1").select();
    document.execCommand("copy");
}

function rm_row(element)
{
   jQuery("#uparow1_"+element).remove();
}

function rm_header_row(element)
{
   jQuery("#uparow2_"+element).remove();
}

function rm_body_row(element)
{
   jQuery("#uparow3_"+element).remove();
}

function select_body_type()
{
    var RequestType = document.getElementById("request_body_type").value;
    if(RequestType=='x-www-form-urlencode')
    {
        jQuery('#json_body').hide();
        jQuery('#x-www-body').show();

    }else if(RequestType=='JSON')
    {
        jQuery('#json_body').show();
        jQuery('#x-www-body').hide();

    }
}

function check_values()
{
    if(jQuery('#multiple-checkboxes').val()=='' || jQuery('#multiple-checkboxes').val()===null)
    {
        alert('Please select atleast one column here');
        exit();
    }
    
    jQuery('#create_api_form').submit();
}

jQuery(document).ready(function() {


jQuery('#multiple-checkboxes').multiselect({
    includeSelectAllOption: true,
    enableFiltering: true
  }); 
});

function displayFileName() {
    var fileInput = document.getElementById('fileInput');
    var file = fileInput.files[0];

    if (file && file.name.endsWith('.json')) {
        document.getElementById('fileName').textContent = file.name; 
    } else {
        document.getElementById('fileName').textContent = "Please select a .json file.";
    }
}

function close_popup()
{
    jQuery('#close_popup').submit(); 
}

function delete_api()
{
    jQuery('#delete_api').submit(); 
}


let deleteModalForm = null;

function showDeleteModal(form) {
    const apiName = form.querySelector('input[name="api_name"]').value;
    const modal = document.getElementById('delete_api_modal');
    
    document.getElementById('api_name_placeholder').textContent = apiName;
    modal.classList.add('show-modal');
    document.body.style.overflow = 'hidden';
    deleteModalForm = form;
}

function hideDeleteModal() {
    const modal = document.getElementById('delete_api_modal');
    modal.classList.remove('show-modal');
    document.body.style.overflow = 'auto';
    deleteModalForm = null;
}

function submitDeleteForm() {
    if (deleteModalForm) {
        deleteModalForm.submit();
    }
    hideDeleteModal();
}

// Initialize modal events when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('delete_api_modal');
    
    // Ensure modal is hidden immediately on page load
    if (deleteModal) {
        deleteModal.classList.remove('show-modal');
        
        // Close modal when clicking outside
        deleteModal.onclick = function(event) {
            if (event.target === this) {
                hideDeleteModal();
            }
        }
    }

    // Close on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && deleteModal && deleteModal.classList.contains('show-modal')) {
            hideDeleteModal();
        }
    });

    // Reset any global variables
    deleteModalForm = null;
    document.body.style.overflow = 'auto';
});

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'mo_field_error mo_field_error_show';
    errorDiv.innerHTML = message;
    
    // Remove existing error message if any
    const existingError = field.parentElement.querySelector('.mo_field_error');
    if (existingError) {
        existingError.remove();
    }
    
    // Highlight the field and add error message
    field.classList.add('mo_input_highlight');
    field.parentElement.appendChild(errorDiv);
    
    // Scroll to first error if this is the first one
    if (!window.firstError) {
        window.firstError = field;
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function check_values() {
    const createApiForm = document.getElementById('create_api_form');
    if (createApiForm) {
        window.firstError = null;
        validateAndSave();
    } else {
        if (typeof window.utilityCheckValues === 'function') {
            window.utilityCheckValues();
        }
    }
}

function hidemodal() {
    const modal = document.getElementById('myModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function toggleFeatures(id) {
    const list = document.getElementById(id + '-list');
    const arrow = list.previousElementSibling.querySelector(".mo_customapi_feature_arrow i");
    
    if (list.style.display === 'none' || list.style.display === '') {
        list.style.display = 'block';
        arrow.classList.remove("fa-chevron-down");
        arrow.classList.add("fa-chevron-up");
    } else {
        list.style.display = 'none';
        arrow.classList.remove("fa-chevron-up");
        arrow.classList.add("fa-chevron-down");
    }
}

function toggleCollapse(contentId, iconElement) {
    let content = document.getElementById(contentId);
    if (content.style.display === "none" || content.style.display === "") {
        content.style.display = "block";
        iconElement.textContent = "-";
    } else {
        content.style.display = "none";
        iconElement.textContent = "+";
    }
}

function toggleApiType() {
    const parameterBasedRadio = document.getElementById('opt1');
    const sqlBasedRadio = document.getElementById('opt2');
    const sqlQuerySection = document.getElementById('sql_query_section');
    const tableSelectionSection = document.getElementById('table_selection_section');
    const columnsSelectionSection = document.getElementById('columns_selection_section');
    const conditionsSection = document.getElementById('conditions_section');
    const filtersSection = document.getElementById('filters_section');
    const saveButton = document.getElementById('save_api_button');
    
    if (sqlBasedRadio && sqlBasedRadio.checked) {
        // Show SQL query textarea
        if (sqlQuerySection) sqlQuerySection.style.display = 'flex';
        
        // Hide parameter-based fields
        if (tableSelectionSection) tableSelectionSection.style.display = 'none';
        if (columnsSelectionSection) columnsSelectionSection.style.display = 'none';
        if (conditionsSection) conditionsSection.style.display = 'none';
        if (filtersSection) filtersSection.style.display = 'none';
        
        // Disable save button
        if (saveButton) {
            saveButton.disabled = true;
            saveButton.style.opacity = '0.6';
            saveButton.style.cursor = 'not-allowed';
        }
    } else if (parameterBasedRadio && parameterBasedRadio.checked) {
        // Hide SQL query textarea
        if (sqlQuerySection) sqlQuerySection.style.display = 'none';
        
        // Show parameter-based fields
        if (tableSelectionSection) tableSelectionSection.style.display = 'flex';
        if (columnsSelectionSection) columnsSelectionSection.style.display = 'flex';
        if (conditionsSection) conditionsSection.style.display = 'block';
        if (filtersSection) filtersSection.style.display = 'block';
        
        // Enable save button
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.style.opacity = '1';
            saveButton.style.cursor = 'pointer';
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleApiType();
});

// Phone dropdown + timezone capture (support + trial forms)
document.addEventListener('DOMContentLoaded', function () {
    if (typeof countries === 'undefined' || !Array.isArray(countries)) {
        return;
    }

    const containers = document.querySelectorAll('[data-mo-phone-dropdown]');
    if (!containers || containers.length === 0) {
        return;
    }

    function normalizeForSearch(value) {
        return String(value || '').trim().toLowerCase();
    }

    function getFlagEmoji(countryCode) {
        if (!countryCode || typeof countryCode !== 'string' || countryCode.length !== 2) {
            return '';
        }
        const code = countryCode.toUpperCase();
        const A = 65;
        const REGIONAL_INDICATOR_A = 0x1F1E6; // 🇦
        const first = code.charCodeAt(0) - A + REGIONAL_INDICATOR_A;
        const second = code.charCodeAt(1) - A + REGIONAL_INDICATOR_A;
        try {
            return String.fromCodePoint(first, second);
        } catch (e) {
            return '';
        }
    }

    function initOne(container) {
        const list = container.querySelector('.mo-country-list');
        const select = container.querySelector('.mo-country-select');
        const hiddenInput = container.querySelector('.mo-country-code');
        if (!list || !select || !hiddenInput) {
            return;
        }

        // timezone hidden fields
        const tzEl = container.querySelector('.mo-client-timezone');
        const offsetEl = container.querySelector('.mo-client-timezone-offset');
        let tzName = '';
        try {
            tzName = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
        } catch (e) {
            tzName = '';
        }
        const offsetMinutes = new Date().getTimezoneOffset();
        if (tzEl) tzEl.value = tzName;
        if (offsetEl) offsetEl.value = String(offsetMinutes);

        function setSelectedCountry(country) {
            const flagEl = select.querySelector('.flag');
            const dialEl = select.querySelector('.dial-code');
            if (!flagEl || !dialEl) return;
            flagEl.className = 'flag';
            flagEl.textContent = getFlagEmoji(country.code);
            dialEl.textContent = `+${country.dial}`;
            hiddenInput.value = String(country.dial);
        }

        const searchLi = document.createElement('li');
        searchLi.className = 'mo-country-search';
        searchLi.innerHTML = `
            <input
                type="text"
                class="mo-country-search-input"
                placeholder="Search country or code…"
                autocomplete="off"
                spellcheck="false"
            />
        `;
        list.appendChild(searchLi);
        const searchInput = searchLi.querySelector('input');

        countries.forEach(country => {
            const li = document.createElement('li');
            li.dataset.name = normalizeForSearch(country.name);
            li.dataset.code = normalizeForSearch(country.code);
            li.dataset.dial = normalizeForSearch(country.dial);
            li.innerHTML = `
                <span class="flag" aria-hidden="true">${getFlagEmoji(country.code)}</span>
                <span class="name">${country.name}</span>
                <span class="dial">+${country.dial}</span>
            `;
            li.onclick = function () {
                setSelectedCountry(country);
                list.classList.remove('open');
            };
            list.appendChild(li);
        });

        const currentDial = String(hiddenInput.value || '').replace(/\D/g, '');
        const initial = countries.find(c => String(c.dial) === currentDial) || countries[0];
        if (initial) {
            setSelectedCountry(initial);
        }

        function applyFilter() {
            if (!searchInput) return;
            const q = normalizeForSearch(searchInput.value);
            const items = list.querySelectorAll('li');
            items.forEach(function (li) {
                if (li === searchLi) return;
                if (!li.dataset) return;
                if (q === '') {
                    li.style.display = '';
                    return;
                }
                const haystack = `${li.dataset.name || ''} ${li.dataset.code || ''} ${li.dataset.dial || ''}`;
                li.style.display = haystack.includes(q) ? '' : 'none';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilter);
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    applyFilter();
                    list.classList.remove('open');
                }
            });
        }

        select.onclick = () => {
            const isOpening = !list.classList.contains('open');
            list.classList.toggle('open');
            if (isOpening && searchInput) {
                searchInput.value = '';
                applyFilter();
                setTimeout(() => searchInput.focus(), 0);
            }
        };

        document.addEventListener('click', function (e) {
            if (!select.contains(e.target) && !list.contains(e.target)) {
                list.classList.remove('open');
            }
        });
    }

    containers.forEach(initOne);
});