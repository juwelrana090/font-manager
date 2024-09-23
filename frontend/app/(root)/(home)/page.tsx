
import { getFonts } from "@/lib/actions";
import FontList from "@/components/fonts/FontList";

interface Props {
  searchParams: {
    [key: string]: string | undefined
  }
}

const HomePage = async ({ searchParams }: Props) => {

  const fonts = await getFonts({
    page: Number(searchParams?.page) || 1
  });

  return (
    <div className="w-full mt-2 mb-2">
      <div className="w-full">
        <h1 className="text-3xl text-black font-normal">Our Fonts</h1>
        <p className="text-base text-gray-500 font-normal">
          Browse a list of Zepto fonts build your font group.
        </p>
      </div>
      <div className="w-full">
        <FontList className="w-full mt-4 mb-2" fonts={fonts} />
      </div>
    </div>
  );
}

export default HomePage;