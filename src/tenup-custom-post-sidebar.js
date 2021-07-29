const { __ } = wp.i18n;
const { compose } = wp.compose;
const { withSelect, withDispatch } = wp.data;
const { PluginDocumentSettingPanel } = wp.editPost;
const { SelectControl, TextControl, PanelRow } = wp.components;

const PostPrimaryCat = ({
  postType,
  postMeta,
  setPostMeta,
  postCateGories,
}) => {
  if ("post" !== postType) return null; // Will only render component for post type 'post'
  console.log(postMeta);
  return (
    <PluginDocumentSettingPanel>
      <PanelRow>
        <SelectControl
          label="Set Primary Category"
          value={postMeta._sp_cat}
          onChange={(value) => setPostMeta({ _sp_cat: value })}
          options={
            postCateGories &&
            postCateGories.map((category) => ({
              value: category.id,
              label: category.name,
            }))
          }
        />
      </PanelRow>
    </PluginDocumentSettingPanel>
  );
};

export default compose([
  withSelect((select) => {
    return {
      postMeta: select("core/editor").getEditedPostAttribute("meta"),
      postType: select("core/editor").getCurrentPostType(),
      postCateGories: select("core").getEntityRecords("taxonomy", "category", {
        per_page: -1,
      }),
    };
  }),
  withDispatch((dispatch) => {
    return {
      setPostMeta(newMeta) {
        console.log(newMeta);
        dispatch("core/editor").editPost({ meta: newMeta });
        //console.log(newMeta);
      },
    };
  }),
])(PostPrimaryCat);
