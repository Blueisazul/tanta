<!-- ---
import { getCollection } from 'astro:content';
import type { CollectionEntry } from 'astro:content';

const ocasiones: CollectionEntry<'ocasiones'>[] = await getCollection('ocasiones');
--- -->

<!-- Ni idea para que se puede usar -->

<section class="mx-auto px-4 max-w-screen-xl mt-20">
  <div class="flex flex-col sm:flex-row justify-between gap-6 md:gap-10">
    {ocasiones.map((ocasion) => {
      const { slug, data } = ocasion;
      const { img, title, impact, description, btn } = data;

      return (
        <article
          class="bg-cover bg-center bg-text-left p-6 text-black rounded-lg shadow-lg flex-1 duration-300 hover:scale-105"
          style={`background-image: url('${img}');`}
        >
          <div class="py-10 px-4 flex flex-col justify-between h-full text-black">
            <div>
              <h3 class="text-xl font-semibold">{title}</h3>
              <p class="text-lg font-bold">{impact}</p>
              <p class="text-sm">{description}</p>
            </div>
            <button class="mt-4 px-6 py-2 bg-amber-500 rounded-lg text-white shadow-md hover:bg-amber-600 transition w-full sm:w-auto">
              {btn}
            </button>
          </div>
        </article>
      );
    })}
  </div>
</section>
