const { registerPlugin } = wp.plugins;
import PostPrimaryCat from "./tenup-custom-post-sidebar";

registerPlugin("my-custom-postmeta-plugin", {
  render() {
    return <PostPrimaryCat />;
  },
});
